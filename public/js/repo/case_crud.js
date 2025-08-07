$("#test_cases_list").sortable({
    update: function (e, u) {
        updateCasesOrder();
    }
});

function updateCasesOrder() {
    var order = [];
    $('#test_cases_list .test_case').each(function (index, element) {
        order.push({
            id: $(this).attr('data-case_id'),
            order: index + 1
        });
    });
    console.log(order);

    $.ajax({
        url: "/tcuo",
        type: 'post',
        data: {
            order: order
        },
        success: function (result) {
        }
    });
}

/****************************************************************************
 * Get data from CREATE and UPDATE test case form. Save data in object
 ****************************************************************************/

function getTestCaseDataFromForm() {
    let testCase = {};

    testCase.id = Number.parseInt($("#tce_case_id").val() || $("#tce_case_id").text(), 10);
    testCase.title = $("#tce_title_input").val();
    testCase.suite_id = $("#tce_test_suite_select").val();
    testCase.automated = $("#tce_automated_select").val();
    testCase.automated_status = $("#tce_automated_status_select").val();
    testCase.script_name = $("#tce_script_name_input").val();
    testCase.priority = $("#tce_priority_select").val();
    testCase.severity = $("#tce_severity_select").val();
    testCase.requirements = $("#tce_requirements_input").val();
    testCase.assignee = $("#tce_assignee_select").val();

    let components = $("#tce_components_select").val();
    if (components) {
        testCase.components = components;
    }

    testCase.data = {};
    testCase.data['preconditions'] = $("#tce_preconditions_input").val();
    testCase.data.steps = [];

    $($(".step")).each(function (index) {

        if ($(this).find(".step_action").val() || $(this).find(".step_result").val()) {
            testCase.data.steps[index] =
                {
                    action: $(this).find(".step_action").val(),
                    result: $(this).find(".step_result").val()
                };
        }
    });
    return testCase;
}

/****************************************************************************
 * CREATE TEST CASE - server returns:
 *      test case tree html element
 *      json of created test case
 ****************************************************************************/

function createTestCase(addAnother = false) {
    let newTestCase = getTestCaseDataFromForm();

    if (!newTestCase.title) {
        warningToast('Title is required', 'Create Test Case');
        return;
    }

    $.ajax({
        type: "POST",
        url: "/test-case/create",
        data: {
            'title': newTestCase.title,
            'suite_id': newTestCase.suite_id,
            'automated': newTestCase.automated,
            'automated_status': newTestCase.automated_status,
            'script_name': newTestCase.script_name,
            'priority': newTestCase.priority,
            'severity': newTestCase.severity,
            'requirements': newTestCase.requirements,
            'components': newTestCase.components,
            'assignee': newTestCase.assignee,
            'order': $('.test_case').length + 1,
            'data': JSON.stringify(newTestCase.data)
        },

        success: function (data) {
            if (data.success == false) {
                let message = ""
                for (const field in data.data) {
                    message += data.data[field];
                }
                errorToast(message,'Create Test Case');
                return;
            }

            let testCase = $.parseJSON(data.json);
            // let testCase = data.json;

            if (addAnother) {
                loadTestCaseCreateForm();
            } else {
                renderTestCase(testCase.id)
            }

            loadCasesList(testCase.suite_id);
        },
        error: () => {errorToast('Some errors appear on server','Create Test Case');}
    });
}

/****************************************************************************
 * UPDATE TEST CASE
 ****************************************************************************/

function updateTestCase() {
    let updatingTestCase = getTestCaseDataFromForm();

    if (!updatingTestCase.title) {
        warningToast('Title is required', 'Create Test Case');
        return;
    }

    $.ajax({
        type: "POST",
        url: "/test-case/update",
        data: {
            'id': updatingTestCase.id,
            'title': updatingTestCase.title,
            'suite_id': updatingTestCase.suite_id,
            'automated': updatingTestCase.automated,
            'automated_status': updatingTestCase.automated_status,
            'script_name': updatingTestCase.script_name,
            'priority': updatingTestCase.priority,
            'severity': updatingTestCase.severity,
            'requirements': updatingTestCase.requirements,
            'components': updatingTestCase.components,
            'assignee': updatingTestCase.assignee,
            'data': JSON.stringify(updatingTestCase.data)
        },
        success: function (data) {  // response is case html and json
            let testCase = $.parseJSON(data.json);
            renderTestCase(testCase.id)
            if (isRepositoryOpened())
                loadCasesList(testCase.suite_id);
        }
    });
}

/****************************************************************************
 * DELETE TEST CASE - delete from list
 ****************************************************************************/
function deleteTestCase(id) {
    $.ajax({
        url: "/test-case/delete",
        method: "POST",
        data: {
            "id": id,
        },
        success: function (data) {
            $("[data-case_id=" + id + "]").remove();

            if ($('#tce_case_id').val() == id || $('#tce_case_id').text() == id) {
                closeTestCaseEditor();
            }
        }
    });
}

