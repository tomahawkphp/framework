<style type="text/css"><?php echo file_get_contents($assetsPath.'profiler.css') ?></style>

<div id="axe-profiler-window-main" class="axe-profiler-window axe-toolbar-reset">
    <div class="axe-profiler-content-area">
        <div id="axe-profiler-log-data" class="axe-profiler-tab-pane axe-profiler-table axe-profiler-log">
            <?php if (count($logs) > 0) : ?>
                <table>
                    <tr>
                        <th>Type</th>
                        <th>Message</th>
                    </tr>
                    <?php foreach ($logs as $log) : ?>
                    <tr>
                        <td class="axe-profiler-table-first">
                            <?php echo $log[0] ?>
                        </td>
                        <td>
                            <?php echo $log[1] ?>
                        </td>
                        <?php endforeach ?>
                    </tr>
                </table>
            <?php else : ?>
                <span class="axe-profiler-empty">There are no log entries.</span>
            <?php endif ?>
        </div>

        <div id="axe-profiler-sql-data" class="axe-profiler-tab-pane axe-profiler-table axe-profiler-sql">
            <?php if (count($queries) > 0) : ?>
                <table>
                    <tr>
                        <th>Time</th>
                        <th>Query</th>
                    </tr>
                    <?php foreach ($queries as $query) : ?>
                        <tr>
                            <td class="axe-profiler-table-first">
                                <?php echo $query['time'] ?>ms
                            </td>
                            <td>
                                <pre><?php echo $query['query'] ?></pre>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </table>
            <?php else : ?>
                <span class="axe-profiler-empty">There have been no SQL queries executed.</span>
            <?php endif ?>
        </div>

        <div id="axe-profiler-time-data" class="axe-profiler-tab-pane axe-profiler-table axe-profiler-checkpoints">
            <?php if (count($timers) > 0) : ?>
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Running Time (ms)</th>
                        <th>Difference</th>
                    </tr>
                    <?php foreach ($timers as $name => $timer) : ?>
                        <tr>
                            <td class="axe-profiler-table-first">
                                <?php echo $name ?>
                            </td>
                            <td><pre><?php echo $timer['running_time'] ?>ms (time from start to render)</pre></td>
                            <td>&nbsp;</td>
                        </tr>

                        <?php if (isset($timer['ticks'])) : ?>
                            <?php foreach( $timer['ticks'] as $tick) : ?>
                                <tr>
                                    <td>
                                        <pre>Tick</pre>
                                    </td>
                                    <td>
                                        <pre><?php echo $tick['time'] ?>ms</pre>
                                    </td>
                                    <td>
                                        <pre>+ <?php echo $tick['diff'] ?>ms</pre>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php else : ?>
                            <tr>
                                <td><pre>Running Time</pre></td>
                                <td><pre><?php echo $timer['time'] ?>ms</pre></td>
                                <td>&nbsp;</td>
                            </tr>
                        <?php endif ?>

                    <?php endforeach ?>
                </table>
            <?php else : ?>
                <span class="axe-profiler-empty">There have been no checkpoints set.</span>
            <?php endif ?>
        </div>
    </div>
</div>

<div id="axe-profiler-main-content" class="axe-toolbar-reset">

    <div class="axe-toolbar-block">
        <div class="axe-toolbar-icon">
            <a href="#">
                <img alt="" src="data:image/png;base64,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" />
                <span><?php echo $version ?></span>
            </a>
        </div>
    </div>
    <div class="axe-toolbar-block">
        <div class="axe-toolbar-icon">
            <a id="axe-profiler-log" class="axe-profiler-tab" href="#">
                <span>
                    <span title="Log">Log</span>
                    <span class="axe-toolbar-status"><?php echo count($logs) ?></span>
                </span>
            </a>
        </div>
    </div>

    <div class="axe-toolbar-block">
        <div class="axe-toolbar-icon">
            <a id="axe-profiler-sql" class="axe-profiler-tab" href="#">
                <span>
                    <span title="Queries">Queries</span>
                    <span class="axe-toolbar-status"><?php echo count($queries) ?></span>
                    <?php if (count($queries)) : ?>
                        <span class="axe-toolbar-status"><?php echo array_sum(array_map(function($q) { return $q['time']; }, $queries)) ?>ms</span>
                    <?php endif ?>
                </span>
            </a>
        </div>
    </div>

    <div class="axe-toolbar-block">
        <div class="axe-toolbar-icon">
            <a class="axe-profiler-tab">
                <span>
                    <span title="Memory">Memory</span>
                    <span class="axe-toolbar-status"><?php echo $memory ?> (<?php echo $memory_peak ?>)</span>
                </span>
            </a>
        </div>
    </div>

    <div class="axe-toolbar-block">
        <div class="axe-toolbar-icon">
            <a id="axe-profiler-time" class="axe-profiler-tab" href="#">
                <span>
                    <span title="Time">Time</span>
                    <span class="axe-toolbar-status"><?php echo $time ?>ms</span>
                </span>
            </a>
        </div>
    </div>

    <a id="main-close-button" class="hide-button" title="Close Toolbar"></a>
</div>

<div id="axe-toolbar-mini" class="axe-toolbar-reset">
    <div class="axe-toolbar-block">
        <a id="show-button">
            <img alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAAAaCAYAAACpSkzOAAAKQWlDQ1BJQ0MgUHJvZmlsZQAASA2dlndUU9kWh8+9N73QEiIgJfQaegkg0jtIFQRRiUmAUAKGhCZ2RAVGFBEpVmRUwAFHhyJjRRQLg4Ji1wnyEFDGwVFEReXdjGsJ7601896a/cdZ39nnt9fZZ+9917oAUPyCBMJ0WAGANKFYFO7rwVwSE8vE9wIYEAEOWAHA4WZmBEf4RALU/L09mZmoSMaz9u4ugGS72yy/UCZz1v9/kSI3QyQGAApF1TY8fiYX5QKUU7PFGTL/BMr0lSkyhjEyFqEJoqwi48SvbPan5iu7yZiXJuShGlnOGbw0noy7UN6aJeGjjAShXJgl4GejfAdlvVRJmgDl9yjT0/icTAAwFJlfzOcmoWyJMkUUGe6J8gIACJTEObxyDov5OWieAHimZ+SKBIlJYqYR15hp5ejIZvrxs1P5YjErlMNN4Yh4TM/0tAyOMBeAr2+WRQElWW2ZaJHtrRzt7VnW5mj5v9nfHn5T/T3IevtV8Sbsz55BjJ5Z32zsrC+9FgD2JFqbHbO+lVUAtG0GQOXhrE/vIADyBQC03pzzHoZsXpLE4gwnC4vs7GxzAZ9rLivoN/ufgm/Kv4Y595nL7vtWO6YXP4EjSRUzZUXlpqemS0TMzAwOl89k/fcQ/+PAOWnNycMsnJ/AF/GF6FVR6JQJhIlou4U8gViQLmQKhH/V4X8YNicHGX6daxRodV8AfYU5ULhJB8hvPQBDIwMkbj96An3rWxAxCsi+vGitka9zjzJ6/uf6Hwtcim7hTEEiU+b2DI9kciWiLBmj34RswQISkAd0oAo0gS4wAixgDRyAM3AD3iAAhIBIEAOWAy5IAmlABLJBPtgACkEx2AF2g2pwANSBetAEToI2cAZcBFfADXALDIBHQAqGwUswAd6BaQiC8BAVokGqkBakD5lC1hAbWgh5Q0FQOBQDxUOJkBCSQPnQJqgYKoOqoUNQPfQjdBq6CF2D+qAH0CA0Bv0BfYQRmALTYQ3YALaA2bA7HAhHwsvgRHgVnAcXwNvhSrgWPg63whfhG/AALIVfwpMIQMgIA9FGWAgb8URCkFgkAREha5EipAKpRZqQDqQbuY1IkXHkAwaHoWGYGBbGGeOHWYzhYlZh1mJKMNWYY5hWTBfmNmYQM4H5gqVi1bGmWCesP3YJNhGbjS3EVmCPYFuwl7ED2GHsOxwOx8AZ4hxwfrgYXDJuNa4Etw/XjLuA68MN4SbxeLwq3hTvgg/Bc/BifCG+Cn8cfx7fjx/GvyeQCVoEa4IPIZYgJGwkVBAaCOcI/YQRwjRRgahPdCKGEHnEXGIpsY7YQbxJHCZOkxRJhiQXUiQpmbSBVElqIl0mPSa9IZPJOmRHchhZQF5PriSfIF8lD5I/UJQoJhRPShxFQtlOOUq5QHlAeUOlUg2obtRYqpi6nVpPvUR9Sn0vR5Mzl/OX48mtk6uRa5Xrl3slT5TXl3eXXy6fJ18hf0r+pvy4AlHBQMFTgaOwVqFG4bTCPYVJRZqilWKIYppiiWKD4jXFUSW8koGStxJPqUDpsNIlpSEaQtOledK4tE20Otpl2jAdRzek+9OT6cX0H+i99AllJWVb5SjlHOUa5bPKUgbCMGD4M1IZpYyTjLuMj/M05rnP48/bNq9pXv+8KZX5Km4qfJUilWaVAZWPqkxVb9UU1Z2qbapP1DBqJmphatlq+9Uuq43Pp893ns+dXzT/5PyH6rC6iXq4+mr1w+o96pMamhq+GhkaVRqXNMY1GZpumsma5ZrnNMe0aFoLtQRa5VrntV4wlZnuzFRmJbOLOaGtru2nLdE+pN2rPa1jqLNYZ6NOs84TXZIuWzdBt1y3U3dCT0svWC9fr1HvoT5Rn62fpL9Hv1t/ysDQINpgi0GbwaihiqG/YZ5ho+FjI6qRq9Eqo1qjO8Y4Y7ZxivE+41smsImdSZJJjclNU9jU3lRgus+0zwxr5mgmNKs1u8eisNxZWaxG1qA5wzzIfKN5m/krCz2LWIudFt0WXyztLFMt6ywfWSlZBVhttOqw+sPaxJprXWN9x4Zq42Ozzqbd5rWtqS3fdr/tfTuaXbDdFrtOu8/2DvYi+yb7MQc9h3iHvQ732HR2KLuEfdUR6+jhuM7xjOMHJ3snsdNJp9+dWc4pzg3OowsMF/AX1C0YctFx4bgccpEuZC6MX3hwodRV25XjWuv6zE3Xjed2xG3E3dg92f24+ysPSw+RR4vHlKeT5xrPC16Il69XkVevt5L3Yu9q76c+Oj6JPo0+E752vqt9L/hh/QL9dvrd89fw5/rX+08EOASsCegKpARGBFYHPgsyCRIFdQTDwQHBu4IfL9JfJFzUFgJC/EN2hTwJNQxdFfpzGC4sNKwm7Hm4VXh+eHcELWJFREPEu0iPyNLIR4uNFksWd0bJR8VF1UdNRXtFl0VLl1gsWbPkRoxajCCmPRYfGxV7JHZyqffS3UuH4+ziCuPuLjNclrPs2nK15anLz66QX8FZcSoeGx8d3xD/iRPCqeVMrvRfuXflBNeTu4f7kufGK+eN8V34ZfyRBJeEsoTRRJfEXYljSa5JFUnjAk9BteB1sl/ygeSplJCUoykzqdGpzWmEtPi000IlYYqwK10zPSe9L8M0ozBDuspp1e5VE6JA0ZFMKHNZZruYjv5M9UiMJJslg1kLs2qy3mdHZZ/KUcwR5vTkmuRuyx3J88n7fjVmNXd1Z752/ob8wTXuaw6thdauXNu5Tnddwbrh9b7rj20gbUjZ8MtGy41lG99uit7UUaBRsL5gaLPv5sZCuUJR4b0tzlsObMVsFWzt3WazrWrblyJe0fViy+KK4k8l3JLr31l9V/ndzPaE7b2l9qX7d+B2CHfc3em681iZYlle2dCu4F2t5czyovK3u1fsvlZhW3FgD2mPZI+0MqiyvUqvakfVp+qk6oEaj5rmvep7t+2d2sfb17/fbX/TAY0DxQc+HhQcvH/I91BrrUFtxWHc4azDz+ui6rq/Z39ff0TtSPGRz0eFR6XHwo911TvU1zeoN5Q2wo2SxrHjccdv/eD1Q3sTq+lQM6O5+AQ4ITnx4sf4H++eDDzZeYp9qukn/Z/2ttBailqh1tzWibakNml7THvf6YDTnR3OHS0/m/989Iz2mZqzymdLz5HOFZybOZ93fvJCxoXxi4kXhzpXdD66tOTSna6wrt7LgZevXvG5cqnbvfv8VZerZ645XTt9nX297Yb9jdYeu56WX+x+aem172296XCz/ZbjrY6+BX3n+l37L972un3ljv+dGwOLBvruLr57/17cPel93v3RB6kPXj/Mejj9aP1j7OOiJwpPKp6qP6391fjXZqm99Oyg12DPs4hnj4a4Qy//lfmvT8MFz6nPK0a0RupHrUfPjPmM3Xqx9MXwy4yX0+OFvyn+tveV0auffnf7vWdiycTwa9HrmT9K3qi+OfrW9m3nZOjk03dp76anit6rvj/2gf2h+2P0x5Hp7E/4T5WfjT93fAn88ngmbWbm3/eE8/syOll+AAAACXBIWXMAAAsTAAALEwEAmpwYAAACLWlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS40LjAiPgogICA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogICAgICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgICAgICAgICB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOnRpZmY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vdGlmZi8xLjAvIj4KICAgICAgICAgPHhtcDpDcmVhdG9yVG9vbD5BZG9iZSBJbWFnZVJlYWR5PC94bXA6Q3JlYXRvclRvb2w+CiAgICAgICAgIDx0aWZmOllSZXNvbHV0aW9uPjcyPC90aWZmOllSZXNvbHV0aW9uPgogICAgICAgICA8dGlmZjpPcmllbnRhdGlvbj4xPC90aWZmOk9yaWVudGF0aW9uPgogICAgICAgICA8dGlmZjpYUmVzb2x1dGlvbj43MjwvdGlmZjpYUmVzb2x1dGlvbj4KICAgICAgPC9yZGY6RGVzY3JpcHRpb24+CiAgIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+CuJJMykAAAPOSURBVEgNnVbPa1RXFD7nvDc/giaCtrbVOAkRK+0kTssoGgyYILpwlY3uhNaNSiskdF9c9A/oprS2SAx2k2RZBIsxTrELFw4m8cdOmURERChNcEx03r2n57x5D2cyeVftYd677957zvnO9+537xuAZkPtMgCVcoWT07neI9qfhOOeto02CRCOlXJfHLnZVXh6u/tL/itXmNBY9ZM2zBXHhINxJ2pRPGwWaVe7l/ppuie/7wRMmRuDg36j3/GoQ8SURoK0RBGzP7UGII5pAZJKQiOCpXYByxj/8kyurzhUKgXNYHUoz2jxygCB5VePbr23AMUuzOwtmcBmkHbLdam0I78vBIM6symQ2pssESP0SgTSCUKkqg1qPmCvT/7v0119/UNQCs4L2Icw2LQGTZjrdBKBYl+pM/XS2kDW4dMs0NhM55795wVsa/75W2PjHNq+3ZmRfVnoV9baFODutIfj13KFgfz9+681gdCKmLkJJgIFYRUIAmLleSJN9DcKoCdgWYDxmc6+QwpUF7nIIFkHoVsikM7q8mYIRVj25gtTO0UId+VS8J6Uhxev7Ojdi9YaBBTX/8koBmJJIWLYfPjxvYeBCb6WpHNWJjvI37mF/B9XAbcLRLVlR2uCBnMy0hqVFQNrbjiweLdcBf5KGM3ruBRwVXz+keeU9l3mBIoDJUmYh4vF1FBldrYG5rRhO7p34c4PcjIsW+AN0b6NQ1rapmOlZXbtQLnHTkLZG6jM35KpW39sK35gORipAW/xpBbddw+iotaGvhOjN0FTHG/UPz/as3VTKvitDXHYMKtInfZeQA/yeV9Phivd+Y/bsnihDWl41cgGYxW+294LSDfp1a7PPmln/+esMFmxBuTgRsK6WFxQ7wQkuUL1Xu8sbN+ImV/khBi2Ig8P8Ykc2GMZwGf6qQBm+3nChnKKQaWmG9RaWLrR3Z1NM/7qMx4LQNWOKzL3XWDgqUd0VNmEe0Af1jEnI6VRk+8FIufIdlxIIR5TGRND1Wce7a/MTfhAWTmA0rLX1kn/ZiiRkVagFdYYLAGdkUQdoi6Q64W8ppH9C3cuahrrceTqlkMiI50QMnrzBKRdPn4oIFUiPKcgHP2PIIPRG3MzSgTSalWzEh7IkUMK4hOcO1iZvaRz5eIjZ6z6NJrT2TCaDJEvB+myR/zNgcrcmAYLOD0ql11r34gRPieukUjAbCDy/rXBiry7b/sX5i8LgC6EEg3V3ZLNMeBi1FZjuyx/oc4eXJxrAnHkS5xqAdJl0coD4FWR8vcDi/PjUXTIJDGTkBUHZbyu/QfJsFpD6tnGMwAAAABJRU5ErkJggg==" />
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    var axeProfilerMain = document.getElementById('axe-profiler-main-content'),
        miniToolbar = document.getElementById('axe-toolbar-mini'),
        profilerWindow = document.getElementById('axe-profiler-window-main'),
        axeLogBtn = document.getElementById('axe-profiler-log'),
        axeQueryBtn = document.getElementById('axe-profiler-sql'),
        axeTimeBtn = document.getElementById('axe-profiler-time'),
        hideBtn = document.getElementById('main-close-button'),
        showBtn = document.getElementById('show-button'),
        tabs = document.getElementsByClassName('axe-profiler-tab-pane'),
        closeTabs = function() {

            for (var a = 0; a < tabs.length; a++)
            {
                tabs[a].style.display = 'none';
            }
        },
        openTab = function(selector) {
            document.getElementById(selector).style.display = 'block';
        },
        closeProfiler = function() {
            profilerWindow.style.display = 'none';
        },
        openProfiler = function() {
            profilerWindow.style.display = 'block';
        },
        isOpen = function(selector) {
            return document.getElementById(selector).style.display == 'block';
        };

    showBtn.addEventListener('click', function(e) {
        e.preventDefault();
        axeProfilerMain.style.display = 'block';
        miniToolbar.style.display = 'none';
    }, false);

    hideBtn.addEventListener('click', function(e) {
        e.preventDefault();
        axeProfilerMain.style.display = 'none';
        miniToolbar.style.display = 'block';
    }, false);


    axeLogBtn.addEventListener('click', function(e) {
        e.preventDefault();

        if (isOpen('axe-profiler-log-data'))
        {
            closeProfiler();
            closeTabs();
        }
        else
        {
            closeTabs();
            openTab('axe-profiler-log-data');
            openProfiler();
        }

    }, false);

    axeQueryBtn.addEventListener('click', function(e) {
        e.preventDefault();

        if (isOpen('axe-profiler-sql-data'))
        {
            closeProfiler();
            closeTabs();
        }
        else
        {
            closeTabs();
            openTab('axe-profiler-sql-data');
            openProfiler();
        }

    }, false);

    axeTimeBtn.addEventListener('click', function(e) {
        e.preventDefault();

        if (isOpen('axe-profiler-time-data'))
        {
            closeProfiler();
            closeTabs();
        }
        else
        {
            closeTabs();
            openTab('axe-profiler-time-data');
            openProfiler();
        }

    }, false);


}, false);
</script>
