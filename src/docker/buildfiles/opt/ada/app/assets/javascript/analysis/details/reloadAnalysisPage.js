/*
 * This file is part of the RapidPipeline extension for TYPO3.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 */

/**
 * @author Rene Gropp <rg@tritum.de>
 * @author Tobias Jungmann <tj@tritum.de>
 */

const isAnalysisRunning = document.querySelector('.js-analysis-status-running'),
    isAnalysisPaused = document.querySelector('.js-analysis-status-paused'),
    timeUntilReload = 60000,
    updateInterval = 1000;

let startTime,
    reloadingTimeout,
    updateTimeViewInterval;

// no reloading if analysis is finished (crashed or completed)
if (isAnalysisRunning || isAnalysisPaused) {
    startReloading();
}

function startReloading()
{
    startTime = (new Date()).getTime();
    reloadingTimeout = setReloadingTimeout();
    updateTimeViewInterval = setUpdateTimeViewInterval();
    updateReloadingView(true);
}

function updateReloadingView(isReloading)
{
    document.querySelector('.js-reload-message').hidden = !isReloading;
}

function setReloadingTimeout()
{
    return setTimeout(
        () => reloadPage(),
        timeUntilReload
    );
}

function reloadPage()
{
    window.location.reload();
}

function getRemainingTimeUntilReload()
{
    const elapsedTime = (new Date()).getTime() - startTime;

    return timeUntilReload - elapsedTime;
}

function setUpdateTimeViewInterval()
{
    setTimeView();

    return setInterval(
        () => setTimeView(),
        updateInterval
    )
}

function setTimeView()
{
    const reloadingTimeView = document.querySelector('.js-reloading-time'),
        remainingTimeInSeconds = getRemainingTimeUntilReload() / 1000;

    reloadingTimeView.innerHTML = Math.round(remainingTimeInSeconds).toString();
}
