var dateStart = null;
var dateEnd = null;
var club = [];
var category = [];
const DATE_SEPARATOR = "/";
// default - get 10 events
lim1 = 0;
lim2 = 10;
itemsAtPage = 10; // how many items on the page at the same time, how many to get at each "more events" click
eventCount = 0; // event count on the page
eventArea = $("#events"); // event area
function process(funcName, data) {
    var ret;
    $.ajax({
        type: "POST",
        url: "http://localhost/BUEvents/AjaxHandler.php",
        async: false,
        data: {
            'function': funcName,
            'data': data
        },
        dataType: "json",
        success: function (returnData) {
            ret = returnData;
        }
    });
    return ret;
}


String.prototype.capitalize = function () {
    return this.charAt(0).toUpperCase() + this.slice(1);
};

function getEvents(s, e) {
    var results = process("getEvents", [
        s, e
    ]);
    appendEvents(results);
    lim1 = lim2 + 1;
    lim2 += itemsAtPage;
}

function clearEvents() {
    eventArea.html("");
    eventCount = 0;
}

function appendEvents(events) {
    if (typeof events == 'string') events = [events];
    for (var i in events) {
        if (events.hasOwnProperty(i)) {
            console.log(events[i]["cover"]);
            var date = events[i]["start_time"].split(" ");
            var hour = date[1].split(":");
            hour = hour[0] + ":" + hour[1];
            var pattern = "<!-- event -->" +
                '<div class="col-xs-12 col-sm-12 col-md-6"><div class="card text-center" data-background="image">' +
                '<div style="background: url(' + events[i]["cover"]["source"] + ') repeat-x; background-size: cover; width:100%; height:100%; position:absolute; opacity: 0.6; -webkit-filter: blur(5px); -moz-filter: blur(5px); -o-filter: blur(5px); -ms-filter: blur(5px); filter: blur(5px);"></div>' + // blurry back bg
                '<div style="background-image: url(' + events[i]["cover"]["source"] + '); background-size: contain; background-position: 50% 50%; background-repeat:no-repeat; width:100%; height:100%; position:absolute;"></div>' + // front bg
                '<div class="header title title-modern text-left" style="margin-left:20px;">' + parseDate(date[0]) + ' - ' + hour + '</div>' +
                '<div class="footer btn-center">' +
                '<h4 class="title title-modern" style="margin-bottom:5px">' + events[i]["name"] + '</h4>' +
                '<h5 class="title title-modern" style="margin-top:0"><i style="opacity:0.8">by ' + events[i]["owner"].capitalize() + '</i></h5>';
            if (events[i]["ticket_uri"] == null)
                events[i]["ticket_uri"] = "http://www.facebook.com/" + events[i]["fbpageid"];
            pattern += '<a class="btn btn-neutral btn-round btn-modern" href="' + events[i]["ticket_uri"] + '" target="_blank">Get a Ticket</a>' +
                '<button class="btn btn-neutral btn-round btn-modern" data-toggle="modal" data-target="#event' + (eventCount++) + '" style="margin-left:10px">&nbsp; Details &nbsp;</button>' +
                '</div>' +
                '<div class="filter filter-blue"></div>' +
                '</div></div>' +
                '<!-- end event -->';
        }
        eventArea.append(pattern);
    }
}

function parseDate(date) {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    var dates = date.split("-");
    if (dates.length != 3) return false;
    var datedd = dates[2];
    var datemm = dates[1];
    var dateyyyy = dates[0];
    if (datedd == dd && datemm == mm && dateyyyy == yyyy) return "Today";
    today.setDate(today.getDate() - 1);
    dd = today.getDate();
    mm = today.getMonth() + 1;
    yyyy = today.getFullYear();
    if (datedd == dd && datemm == mm && dateyyyy == yyyy) return "Yesterday";
    return datedd + DATE_SEPARATOR + datemm + DATE_SEPARATOR + dateyyyy;
}

$(document).ready(function () {
    var clubDivider = $('ul[aria-labelledby="clubs"]').find('li[class="divider"]');
    var catDivider = $('ul[aria-labelledby="categories"]').find('li[class="divider"]');
    var i1 = 3;
    var clubs = process("getClubs", null);
    for (var i = clubs.length - 1; i > -1; i--) {
        if (clubs.hasOwnProperty(i))
            clubDivider.after('<li class="single"><label class="checkbox" for="checkbox' + i1 + '" style="margin-left: 5%"><span class="icons"><span class="first-icon fa fa-square fa-base"></span><span class="second-icon fa fa-check-square fa-base"></span></span><input type="checkbox" value="' + clubs[i] + '" id="checkbox' + (i1++) + '" data-toggle="checkbox">' + clubs[i].capitalize() + '</label></li>');
    }

    var categories = process("getCategories", null);
    for (i = categories.length - 1; i > -1; i--) {
        if (categories.hasOwnProperty(i))
            catDivider.after('<li class="single"><label class="checkbox" for="checkbox' + i1 + '" style="margin-left: 5%"><span class="icons"><span class="first-icon fa fa-square fa-base"></span><span class="second-icon fa fa-check-square fa-base"></span></span><input type="checkbox" value="' + categories[i] + '" id="checkbox' + (i1++) + '" data-toggle="checkbox">' + categories[i].capitalize() + '</label></li>');
    }

    // club - category dropdowns

    $("#allClubs").on("click", '.all', function () {
        if ($(this).hasClass("checked")) return;
        var all = $('#allClubs').find('.single label').each(function (i, item) {
            if ($(item).hasClass("checked"))
                $(item).removeClass("checked");
            var input = $(item).find("input");
            if (input.prop("checked"))
                input.removeProp("checked");
        });
    });

    $("#allCategories").on("click", '.all', function () {
        if ($(this).hasClass("checked")) return;
        var all = $('#allCategories').find('.single label').each(function (i, item) {
            if ($(item).hasClass("checked"))
                $(item).removeClass("checked");
            var input = $(item).find("input");
            if (input.prop("checked"))
                input.removeProp("checked");
        });
    });

    $(".row").on("click", ".single", function () {
        var all = $(this).parent().find('li label[class~="all"]');
        if (all.hasClass("checked"))
            all.removeClass("checked");
        var input = all.find("input");
        if (input.prop("checked"))
            input.removeProp("checked");
    });

    // filter

    $("#filter").on("click", function () {
        var checkedClubs = [];
        var checkedCategories = [];

        $('ul[aria-labelledby="clubs"]').find('li label[class="checkbox checked"] input').each(function (i, club) {
            checkedClubs[i] = $(club).val();
        });
        if (checkedClubs.length == 0) checkedClubs = ["all"];
        if (checkedCategories.length == 0) checkedCategories = ["all"];
        $('ul[aria-labelledby="categories"]').find('li label[class="checkbox checked"] input').each(function (i, cat) {
            checkedCategories[i] = $(cat).val();
        });
        var date = [$('#start_time').val(), $('#end_time').val()];
        var result = process("filter", {
            clubs: checkedClubs,
            categories: checkedCategories,
            date: date
        });
    });

    $("#search").on("click", function () {
        var searchVal = $("#searchField").val();
        process("search", searchVal);
    });

    getEvents(lim1, lim2);

});


