/****************************************************************************
 * LIGHTBOX - Show lightbox for resized image
 *****************************************************************************/

$("body").on("click", "#test_case_content img, .document img", function () {
    var imageSrc = $(this).attr('src');
    $("#any_img_lightbox_image").attr("src", imageSrc);
    $("#any_img_lightbox").modal('show');
});


/****************************************************************************
 * TEXTAREA RESIZE - steps and preconditions in case editor
 *****************************************************************************/

$.fn.autoResize = function () {
    let r = e => {
        e.style.height = '';
        e.style.height = e.scrollHeight + 'px'
    };
    return this.each((i, e) => {
        e.style.overflow = 'hidden';
        r(e);
        $(e).bind('input', e => {
            r(e.target);
        })
    })
};

$(document).ready(function () {

    $('.alert').show('fade', 500);
    setTimeout(removeAlert, 3000);

    $('body').on('click', '.alert button', function () {
        removeAlert();
    });

    function removeAlert() {
        $('.alert').hide('fade', 500);
        setTimeout(function () {
            $('.alert').remove();
        }, 500);
    }
});

var testCaseJson; // и переменная должна быть глобальной
function loadTestCaseJson(id) {
    $.ajax({
        type: 'GET',
        url: '/test-case/get',
        async: false,  // без этого не будет работать return
        data: {id: id},

        success: function (data) {
            testCaseJson = $.parseJSON(data);
        }
    });
}

function sortSuitesByParentId2(repository_id) {
    var childSuiteHtml;

    $($("[data-test_suite_id]")).each(function (index) {
        console.log('sad')
        let parent_id = $(this).attr('data-parent_id'); // достать parent_id

        if (parent_id != repository_id) {  // не ставить !== иначе все сломается
            // childSuiteHtml = "<ul>" + $(this).prop('outerHTML').toString() + "</ul>"; // сохранить код элемента
            // childSuiteHtml = "<li>" + $(this).prop('outerHTML').toString() + "</li>";
            childSuiteHtml = $(this).prop('outerHTML').toString();
            $(this).remove();
            $(`[data-test_suite_id=${parent_id}]`).append(childSuiteHtml);
        }
    });
}

function sortTreeByParentId() {

    var childSuiteHtml;

    $($(".tree_suite")).each(function (index) {
        let parent_id = $(this).attr('data-parent_id'); // достать parent_id
        let parentSuiteLocator = `.tree_test_suite[data-test_suite_id="${parent_id}"]`

        childSuiteHtml = $(this).prop('outerHTML').toString();

        if ($(parentSuiteLocator).length > 0) {
            $(parentSuiteLocator).append(childSuiteHtml);
            $(this).remove();
        }
    });
}


/**************************************************
 * Check if repository page is opened
 *************************************************/

function isRepositoryOpened() {
    return $('#suites_tree_col').is(":visible")
}


/****************************************************************************
 * RESIZABLE for test case viewer
 *****************************************************************************/
//
// interact('.resizable')
//     .resizable({
//         edges: { top: false, left: true, bottom: false, right: false },
//         listeners: {
//             move: function (event) {
//                 let { x, y } = event.target.dataset
//
//                 x = (parseFloat(x) || 0) + event.deltaRect.left
//                // y = (parseFloat(y) || 0) + event.deltaRect.top
//
//                 Object.assign(event.target.style, {
//                     width: `${event.rect.width}px`,
//                     // height: `${event.rect.height}px`,
//                     // transform: `translate(${x}px, ${y}px)`
//                 })
//
//                 Object.assign(event.target.dataset, { x })
//             }
//         }
//     })


function closeTestCaseOverlay() {
    $("#test_case_overlay").modal('hide');
}

function createIssueList(issueInput, issuesInput, issuesList) {
    $(issueInput).autocomplete({
        filterMinChars: 5,
        filterDelay: 1000,
        filterAjaxParams: {
            headers: {
                'Authorization': `Bearer ${jiraToken}`,
                'Content-Type': 'application/json'
            }
        },
        labelKey: 'summary',
        itemRenderer: function(dropdown, data) {
            var item = $('<a class="dropdown-item" href="#"></a>');;
            item.data(data);
            item.html('[<a href="#">' + data.key + '</a>] ' + data[this.labelKey]).appendTo(dropdown);
            return item;
        },
        preProcess: function(resp) {
            let res = [];
            if (resp instanceof Array)
                for (const item of resp) {
                    res.push({
                        key: item.key,
                        summary: item.fields.summary,
                        issuetype: resp.fields.issuetype.name,
                        status: resp.fields.status.name,
                        iconUrl: item.fields.issuetype.iconUrl
                    });
                }
            else if (typeof resp == 'object')
                res.push({
                    key: resp.key,
                    summary: resp.fields.summary,
                    issuetype: resp.fields.issuetype.name,
                    status: resp.fields.status.name,
                    iconUrl: resp.fields.issuetype.iconUrl
                });

            return res;
        },
        onPick(input, item) {
            var data = $(item).data();

            var value = $(issuesInput).val();
            if (!value.includes(data.key)) {
                value = value + (value == '' ? '' : ',') + data.key;
                $(issuesInput).val(value);

                $(issuesList).issuelist("_fetchPresetData", data);
                $(issuesList).issuelist("refresh");
            }

            $(input).val(null);
        }
    });

    $(issuesList).issuelist({
        issueUrl: jiraUrl + '/browse/',
        savedIssueKeys: $(issuesInput).val(),
        prefetch: jiraUrl + '/rest/api/2/search?jql=issuekey in (%%ISSUES%%)&fields=summary,priority,status,issuetype',
        preProcess: function(resp) {
            var res = [];
            if (resp instanceof Array)
                for (const item of resp) {
                    res.push({
                        key: item.key,
                        summary: item.fields.summary,
                        issuetype: item.fields.issuetype.name,
                        status: item.fields.status.name,
                        iconUrl: item.fields.issuetype.iconUrl
                    });
                };
            return res;
        },
        onBeforeFetch: function(el) {
            $(".mask").removeClass("d-none");
        },
        onAfterFetch: function(el) {
            $(".mask").addClass("d-none");
        },
        onBeforeDelete: function(delItem) {
            var values = $(issuesInput).val().split(',');
            var index = values.indexOf(delItem.data().key);
            if (index >= 0)
                values.splice(index, 1);

            $(issuesInput).val(values.join(','));
        }

    });

}


/**
 Handle clicks for pseudo-elements before/after
 It is wise to make the parent element RELATIVE positioned. If you have an absolute positioned pseudo-element,
 this function will only work if it is positioned based on the parent’s
 dimensions(so the parent has to be relative…maybe sticky or fixed would work too….)
 */
function pseudoClick(parentElem) {

    var beforeClicked,
        afterClicked;

    var parentLeft = parseInt(parentElem.getBoundingClientRect().left, 10),
        parentTop = parseInt(parentElem.getBoundingClientRect().top, 10);

    var before = window.getComputedStyle(parentElem, ':before');

    var beforeStart = parentLeft + (parseInt(before.getPropertyValue("left"), 10)),
        beforeEnd = beforeStart + parseInt(before.width, 10);

    var beforeYStart = parentTop + (parseInt(before.getPropertyValue("top"), 10)),
        beforeYEnd = beforeYStart + parseInt(before.height, 10);

    var after = window.getComputedStyle(parentElem, ':after');

    var afterStart = parentLeft + (parseInt(after.getPropertyValue("left"), 10)),
        afterEnd = afterStart + parseInt(after.width, 10);

    var afterYStart = parentTop + (parseInt(after.getPropertyValue("top"), 10)),
        afterYEnd = afterYStart + parseInt(after.height, 10);

    var mouseX = event.clientX,
        mouseY = event.clientY;

    beforeClicked = (mouseX >= beforeStart && mouseX <= beforeEnd && mouseY >= beforeYStart && mouseY <= beforeYEnd ? true : false);
    afterClicked = (mouseX >= afterStart && mouseX <= afterEnd && mouseY >= afterYStart && mouseY <= afterYEnd ? true : false);

    return {
        "before" : beforeClicked,
        "after"  : afterClicked
    };

}

function errorToast(message, header) {
    showToast(message, header, 'danger', 'sign-stop-fill')
}
function warningToast(message, header) {
    showToast(message, header, 'warning', 'exclamation-circle-fill')
}
function infoToast(message, header) {
    showToast(message, header, 'primary', 'info-circle-fill')
}
function successToast(message, header) {
    showToast(message, header, 'success',check-circle)
}

function showToast(message, header, type, icon, isTop) {
    isTop = isTop ?? true;
    var toast,
        type = type ?? "success",
        position = isTop ? "top:60px" : "bottom:20px";

    if (header == undefined) {
        toast = $(
`<div class="toast align-items-center text-bg-${type} show position-fixed end-0 z-3" style="${position}" role="alert" aria-live="assertive" aria-atomic="true">
  <div class="d-flex">
    <div class="toast-body">
    ${message}
   </div>
    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
</div>`);
    } else {
        toast = $(
`<div class="toast show text-bg-${type} position-fixed end-0 z-3" style="${position}" role="alert" aria-live="assertive" aria-atomic="true">
  <div class="toast-header">
    <i class="bi bi-${icon} me-2"></i>
    <strong class="me-auto">${header}</strong>
<!--    <small class="text-body-secondary">11 mins ago</small>-->
    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
  <div class="toast-body">
    ${message}
  </div>
</div>`);
    }

    if (isTop) {
        if ($('.toast').length > 0) {
            $('.toast').first().before(toast);
        } else
            $('body').append(toast);
    } else
        $('body').append(toast);


    var height = $(toast).height() + 10, len = $('.toast').length;

    if (len > 1)
    if (!isTop)
        $('.toast').each((i,el) => {
            if (i < len-1) {
                el.style.bottom = (parseInt(el.style.bottom) + height) + 'px';
            }
        })
    else
        $('.toast').each((i,el) => {
            if (i > 0) {
                el.style.top = (parseInt(el.style.top) + height) + 'px';
            }
        })

    setTimeout(function() {
        toast.remove();
    }, 5000)
}