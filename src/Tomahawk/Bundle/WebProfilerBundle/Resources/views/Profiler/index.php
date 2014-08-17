<style type="text/css"><?php echo file_get_contents($assetsPath.'profiler.css') ?></style>
<div class="axe-profiler">
    <div class="axe-profiler-window">
        <div class="axe-profiler-content-area">
            <div class="axe-profiler-tab-pane axe-profiler-table axe-profiler-log">
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

            <div class="axe-profiler-tab-pane axe-profiler-table axe-profiler-sql">
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

            <div class="axe-profiler-tab-pane axe-profiler-table axe-profiler-checkpoints">
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
        <ul id="axe-profiler-open-tabs" class="axe-profiler-tabs">
            <li><a data-axe-profiler-tab="axe-profiler-log" class="axe-profiler-tab" href="#">Log <span class="axe-profiler-count"><?php echo count($logs) ?></span></a></li>
            <li>
                <a data-axe-profiler-tab="axe-profiler-sql" class="axe-profiler-tab" href="#">QUERIES
                    <span class="axe-profiler-count"><?php echo count($queries) ?></span>
                    <?php if (count($queries)) : ?>
                        <span class="axe-profiler-count"><?php echo array_sum(array_map(function($q) { return $q['time']; }, $queries)) ?>ms</span>
                    <?php endif ?>
                </a>
            </li>
            <li><a class="axe-profiler-tab" data-axe-profiler-tab="axe-profiler-checkpoints">Time <span class="axe-profiler-count"><?php echo $time ?>ms</span></a></li>
            <li><a class="axe-profiler-tab">Memory <span class="axe-profiler-count"><?php echo $memory ?> (<?php echo $memory_peak ?>)</span></a></li>
            <li class="axe-profiler-tab-right"><a id="axe-profiler-hide" href="#">&#8614;</a></li>
            <li class="axe-profiler-tab-right"><a id="axe-profiler-close" href="#">&times;</a></li>
            <li class="axe-profiler-tab-right"><a id="axe-profiler-zoom" href="#">&#8645;</a></li>
        </ul>

        <ul id="axe-profiler-closed-tabs" class="axe-profiler-tabs">
            <li><a id="axe-profiler-show" href="#">&#8612;</a></li>
        </ul>
    </div>
</div>

<script src='//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
<script><?php echo file_get_contents($assetsPath.'profiler.js') ?></script>