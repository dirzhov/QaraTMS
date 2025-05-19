let currentCase;
const testCaseAreaLocator = '#test_case_col'
const chartAreaLocator = '#chart'
const mask = '.mask'

function loadTestCase(test_run_id, test_case_id) {
    $(testCaseAreaLocator).load(`/trc/${test_run_id}/${test_case_id}`, function () {

    });
}

function loadChart(test_run_id) {
    $(chartAreaLocator).load(`/trchart/${test_run_id}`, function () {

    });
}

/*
   const PASSED = 1;
    const FAILED = 2;
    const BLOCKED = 3;
    const NOT_TESTED = 4;
 */
function updateCaseStatus(test_run_id, test_case_id, status, assignee_id, assignee, issues) {
    $(mask).toggleClass("d-none");

    $.ajax({
        type: "POST",
        url: "/trcs",
        data: {
            'test_run_id': test_run_id,
            'test_case_id': test_case_id,
            'status': status,
            'failed_step': $('#steps_container .step.failed').data("badge") ?? "",
            'assignee': assignee_id,
            'issues': issues
        },

        success: function (data) {  // response is case html and json

            var st = null;
            if (status == 1) {
                st = {cls: "bg-success", txt: "Passed"};
            } else if (status == 2) {
                st = {cls: "bg-danger", txt: "Failed"};
            } else if (status == 3) {
                st = {cls: "bg-warning", txt: "Blocked"};
            } else if (status == 4) {
                st = {cls: "bg-secondary", txt: "Not Tested"};
            }
            $(`.result_badge[data-test_case_id='${test_case_id}']`)
                .html('<span class="badge '+ st.cls +' status float-end">' + st.txt + '</span><small class="me-1 text-secondary">' + assignee + '</small>');

            loadChart(test_run_id); // reload chart

            $(mask).toggleClass("d-none");

            // $(".badge.bg-secondary").first().click(); // select next untested case
        },
        error: function (resp) {
            $(mask).toggleClass("d-none");
        }
    });
}

/*
   const PASSED = 1;
    const FAILED = 2;
    const BLOCKED = 3;
    const NOT_TESTED = 4;
 */

var setSelectedBtn = function(status) {

    $('.test_run_case_btn').each(function () {

        var btnStatus = $(this).attr('data-status');

        if (status == 1 && status == btnStatus) {
            $(this).removeClass("btn-outline-success");
            $(this).addClass("btn-success").attr('selected', 'selected');
        } else if (status == 2 && status == btnStatus) {
            $(this).removeClass("btn-outline-danger");
            $(this).addClass("btn-danger").attr('selected', 'selected');
        } else if (status == 3 && status == btnStatus) {
            $(this).removeClass("btn-outline-warning");
            $(this).addClass("btn-warning").attr('selected', 'selected');
        } else if (status == 4 && status == btnStatus) {
            $(this).removeClass("btn-outline-secondary");
            $(this).addClass("btn-secondary").attr('selected', 'selected');
        }

    });

}

var resetAllBtns = function() {

    $('.test_run_case_btn').each(function () {

        var status = $(this).attr('data-status');

        if (status == 1) {
            $(this).removeClass("btn-success").removeAttr('selected');
            $(this).addClass("btn-outline-success");
        } else if (status == 2) {
            $(this).removeClass("btn-danger").removeAttr('selected');
            $(this).addClass("btn-outline-danger");
        } else if (status == 3) {
            $(this).removeClass("btn-warning").removeAttr('selected');
            $(this).addClass("btn-outline-warning");
        } else if (status == 4) {
            $(this).removeClass("btn-secondary").removeAttr('selected');
            $(this).addClass("btn-outline-secondary");
        }

    });

}


$('body').on('click', '.test_run_case_btn', function () {

    resetAllBtns();

    var status = $(this).attr('data-status');

    if (status == 1) {
        $(this).removeClass("btn-outline-success");
        $(this).addClass("btn-success").attr('selected', 'selected');
        $('#steps_container .step').slice(1).removeClass('passed').removeClass('failed')
    } else if (status == 2) {
        $(this).removeClass("btn-outline-danger");
        $(this).addClass("btn-danger").attr('selected', 'selected');
    } else if (status == 3) {
        $(this).removeClass("btn-outline-warning");
        $(this).addClass("btn-warning").attr('selected', 'selected');
        $('#steps_container .step').slice(1).removeClass('passed').removeClass('failed')
    } else if (status == 4) {
        $(this).removeClass("btn-outline-secondary");
        $(this).addClass("btn-secondary").attr('selected', 'selected');
        $('#steps_container .step').slice(1).removeClass('passed').removeClass('failed')
    }
});


$('body').on('click', '.tree_test_case', function () {

    $('.tree_test_case.selected_case').removeClass("selected_case");

    $(this).addClass('selected_case');
})
