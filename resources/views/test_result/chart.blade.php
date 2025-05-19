<div class="autotest-run-progress">
<div class="progress">
    <div class="progress-bar bg-success" role="progressbar" style="width: {{$testRun->getAutomationChartData()['passed'][1]}}%"
         title="Total Passed">
        {{$testRun->getAutomationChartData()['passed'][0]}}
    </div>

    <div class="progress-bar bg-danger" role="progressbar" style="width: {{$testRun->getAutomationChartData()['failed'][1]}}%"
         title="Total Failed">
        {{$testRun->getAutomationChartData()['failed'][0]}}
    </div>

    <div class="progress-bar bg-secondary" role="progressbar" style="width: {{$testRun->getAutomationChartData()['skipped'][1]}}%"
         title="Total Skipped">
        {{$testRun->getAutomationChartData()['skipped'][0]}}
    </div>
</div>
</div>