# AGRIFUTURE Desktop Agent

User documentation can be found on our website: \* [Installation
Instructions](https://agrifuture.senckenberg.de/en/install-instructions)
\* [User Manuall](https://agrifuture.senckenberg.de/en/user-manual)

The sections bellow give a brief introduction for developers.

## Launcher Script

The Launcher Script is a Text User Interface which simplifies the
starting of the Docker Image where the Symfony application is located.
It allows selecting the user language as well as directory where the
analysis will be located to be mounted into the container, downloading
the latest version of the Docker Image with the Symfony application,
starting the application with the required arguments, starts the web
browser with the [Desktop Agent](#desktop-agent) in the language the
user selected, and executes a command to the application when it is
exited to cancel the currently running analysis.

------------------------------------------------------------------------

**Only the Docker Image is upgraded automatically.** Users need to
upgrade the launcher script manually. Avoid breaking changes which
result in a Launcher Script being incompatible with another version of
the Symfony application.

------------------------------------------------------------------------

The Launcher Script is currently maintained for Debian GNU/Linux,
Windows and macOS. The files are located in the Desktop Agent source
code under `src/launcher`.

## Building and Running the Docker Image

### Development Environment

------------------------------------------------------------------------

**This does differ from Production Builds.** Do test the [production
container](#production-builds) before deploying it. As the Docker Image
is automatically updated by the [Launcher Script](#launcher-script),
testing it before deployment is important.

------------------------------------------------------------------------

To develop the application, a [Docker
Compose](https://docs.docker.com/compose/) file is included. To get
started with development, running `docker compse up` is sufficient. It
will then be available [locally on port 8041](http://127.0.0.1:8041/).

Once started, you need to run the following commands **inside the
container**:

- `composer install`
- `composer build:frontend:development`

The development environment behaves similarly to a Desktop Agent where
the directory selected in the [Launcher Script](#launcher-script) equals
to `volumes/data` in the working directory of the source code.
Similarly, the [SQLite](https://www.sqlite.org/index.html) database is
stored in `volumes/fakehome` instead of the platform-dependent path used
by the [Launcher Script](#launcher-script).

Changes to some parts of the application (for example, the
[`app:watch`](#uploading-files-as-they-are-created) command) may require
restarting the application. Some parts of the [Launcher
Script](#launcher-script), such as canceling the current analysis on
shutdown, are not simulated and must be executed manually (for example,
by executing `php bin/console app:cancel-analysis` inside the
container).

You may need to adjust the following environment variables inside
`docker-compose.yml`:

- `ADA_PORTAL_DE` and `ADA_PORTAL_EN` are used to determine the
  base URL of the Portal. The URL must not contain a tailing solidus.
  Appending `/api` should yield a valid Base URL for the API. Both the
  API URL and the URLs to the frontend are derived from these variables,
  with the former being used in the german locale and the latter in
  english.
- `ADA_RUN_UUID` can be changed if you wish to run multiple analysis
  in parallel.

The source code is located in `src/docker/buildfiles/opt/ada/app`.

### Production Builds

Production builds are executed automatically as soon as a tag is created
with GitHub actions. The steps needed to create the build can be
summarized as follows:

- Build the docker image.
- Replace `{{ VERSION }}` in all files which need it, including
  `src/launcher/agrifuture-desktop-agent.sh`.
- Create an installer which moves the files needed for the platform to
  paths where the platform expects such files.

For more details, you may want to check the section about installing the
Desktop Agent from source code on GNU/Linux from the User Manual.

## Authorization with the API

The authorization workflow using the generateToken endpoint is
implemented in `\App\Service\ApiService::generateToken`. Getting
information about the current token as well as checking if they are
still valid using getTokenInformation is implemented in
`\App\Service\ApiService::getTokenInformation`.

The application uses the [Symfony Security
component](https://symfony.com/doc/current/security.html) to manage
authorization. Every page other than the login page is configured to
require a token. The plumbing to support the API authentication is
implemented in `App\Security`.

Once a user is logged in, the token is not only stored in the browser
session, but also in the database in the `token` table. This means users
can use any browser on the local computer and share the same login in
addition to being able to use synchronization features to share a token
on multiple devices. Triggering a logout invalidates both the session in
the currently running browser and removes the token from the database;
other browsers may still be logged in.

## Running an Analysis using the Desktop Agent

Once a user has filled out the
[form](https://symfony.com/doc/6.1/forms.html), all data is sent to the
createAnalysis endpoint in `App\Service\createAnalysis`. If the Portal
conforms the request and the `id` is received, it is stored in the local
database together with the name, last known status, and the directory
selected by the user. As this information is not sufficient to render
the detail page or the current analysis section of the header, the
current analysis status is fetched when needed.

The result of the `getAnalysis` endpoint is locally cached for up to one
minute in order to increase performance. The cache is invalidated every
time the database entry of the analysis or analysis uploads is changed.

Upon starting of the [Desktop Agent](#desktop-agent), the command
`php bin/console app:watcher` is executed. If it ever exits, it is
automatically restarted. It checks in a loop if an analysis is running
by looking in the database, and if it is, it checks the files in the
selected directory. The command is implemented in
`App\Command\WatcherCommand`.

If it contains a file that isn't yet uploaded, the file gets uploaded. A
database entry is created for the uploaded file containing the name and
errors (if an error occurred while uploading). Errors are shown in the
[Desktop Agent](#desktop-agent) when visiting the detail view of the
analysis. All files which were successfully uploaded are ignored in the
next iteration of the loop. The detail page may also compare `uploads`
received from the `getAnalysis` endpoint and warn the user if
differences are detected.

If the received status of the analysis is either finished or crashed,
the [Desktop Agent](#desktop-agent) will know the analysis was finished.
The next time the detail page is loaded, all data associated with the
analysis is removed from the database. This includes the analysis
itself, the database entries for uploaded files and cached `getAnalysis`
results. The user will be redirected to the page for creating a new
analysis and is shown both a translated human-readable version of the
reason provided by the Portal and any errors and warnings encountered
while uploading files.

A user stopping, cancelling or pausing the analysis will use the
updateStatus endpoint to change the status of the analysis. It will also
update the last known status in the local database, potentially
triggering a cleanup once the page is reloaded as described in the
previous paragraph.

If the user interrupts the [production build](#production-builds) of the
[Desktop Agent](#desktop-agent) or if a developer executes
`php bin/console app:cancel-analysis`, the status changes into crashed
and the database is immediately cleaned up as described above.

## Error Pages

If an undesirable state is detected, the request is forwarded to
`App\ControllerErrorController`. The directory
`src/docker/buildfiles/opt/ada/app/templates/pages/error` contains the
templates for these errors. Additionally, the status of the analysis may
show up as "Unknown": if an analysis is running and the current status
cannot be determined.

The following errors are common and have their own error pages:

- [API Access
  Forbidden](http://127.0.0.1:8041/en/error/api-access-forbidden)[^1]:
  This error is generated if an endpoint such as getTokenInformation
  returns status code 403, prompting the user to reconnect their
  account. The most probable cause is that the user is not an End User.
  This is checked on each page load in
  `\App\EventListener\EarlyErrorDetection`. It can also be triggered in
  various controllers.
- [Forbidden](http://127.0.0.1:8041/en/error/forbidden) The getAnalysis
  endpoint can only fetch the information of an analysis which the same
  user started. If a user switches accounts while an analysis is
  running, an attempt to view the running analysis will result on this
  error page. The user is presented with two options: logging in using
  another account or "abandoning" their analysis, meaning the [Desktop
  Agent](#desktop-agent) will forget that the analysis is running
  without canceling it. The error is shown only when the user visits the
  [detail view](http://127.0.0.1:8041/en/analysis) of the analysis.
- [Invalid Token](http://127.0.0.1:8041/en/error/invalid-token)
  indicates that the token is no longer valid. The token was probably
  revoked either in the Portal, or because the user deleted their
  account. This is checked on each page load in
  `\App\EventListener\EarlyErrorDetection`. It can also be triggered in
  various controllers.
- [Offline](http://127.0.0.1:8041/en/error/offline) means the [Desktop
  Agent](#desktop-agent) didn't receive status code 204 from the
  checkInternetConnectivity endpoint. Maybe the user is offline or
  network settings prevent access to the portal. In the [Development
  Environment](#development-environment), it may mean that ADA_PORTAL_DE
  or ADA_PORTAL_EN are wrong or that the local portal hasn't started.

In case an error needs to be debugged, the following tips may be
helpful:

- Check if the Portal has generated any error logs in `var/logs`.
- Debug the response received either in the [Desktop
  Agent](#desktop-agent) in `\App\Service\ApiService` or in the Portal
  before it is sent at the end of
  `\TRITUM\RapidPipeline\Middleware\ApiDispatcher::process`.

[^1]: Any link to the [Desktop Agent](#desktop-agent) assumes it is
    running on port 8041. This is the case for the [Development
    Environment](#development-environment) and *may* be the case for
    [Production Builds](#production-builds). If it isn't, try
    incrementing the port number until it works. If multiple Desktop
    Agents are running at the same time, each will have a distinct port
    starting at 8041.
