dateStart = null;
dateEnd = null;
club = [];
checkedClubs = [];
clubNames = [];
category = [];
checkedCategories = [];
events = [];
moreEventsDeleted = false;

const DATE_SEPARATOR = "/";
// default - get 10 events
itemsAtPage = 10; // how many items on the page at the same time, how many to get at each "more events" click
eventCount = 0; // event count on the page
eventArea = $("#events"); // event area
modalArea = $("#modals"); // modal area
moreEventsField = '<div class="well well-md text-center"> <h3>More Events &nbsp;<i class="fa fa-arrow-circle-down"></i></h3> <!-- a fucking credit to @onrcskn  --> </div>';

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


function checkCategory(cat) {
    if (cat == "All categories") checkedCategories = [];
    if (checkedCategories.length == 1 && checkedCategories.indexOf("All categories") != -1)
        uncheckCategory("All categories");
    if (checkedCategories.indexOf(cat) == -1)
        checkedCategories.push(cat);
    fillDropdownTexts();
}

function uncheckCategory(cat) {
    var i = checkedCategories.indexOf(cat);
    if (i > -1) checkedCategories.splice(i, 1);
    fillDropdownTexts();
}


function checkClub(clubid, clubname) {
    if (clubid == "All clubs") {
        checkedClubs = [];
        clubNames = [];
    }
    if (checkedClubs.length == 1 && checkedClubs.indexOf("All clubs") != -1)
        uncheckClub("All clubs");
    if (checkedClubs.indexOf(clubid) == -1) {
        checkedClubs.push(clubid);
        clubNames.push(clubname);
    }
    fillDropdownTexts();
}

function uncheckClub(club) {
    var i = checkedClubs.indexOf(club);
    if (i > -1) {
        checkedClubs.splice(i, 1);
        clubNames.splice(i, 1);
    }
    fillDropdownTexts();
}

function fillDropdownTexts() {
    $("#clubsText").html(clubNames.join(", "));
    $("#categoriesText").html(checkedCategories.join(", "));
}

function clearEvents() {
    eventArea.html("");
    modalArea.html("");
    eventCount = 0;
}

function getTodayDate() {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    mm = ("0" + mm).slice(-2);
    dd = ("0" + dd).slice(-2);
    return yyyy + "/" + mm + "/" + dd;
}

var listingType = "grid";

function setListingType(type) {
    listingType = type;
}

function moreEvents() {
    if (events.length > 0) {
        if (events.length <= itemsAtPage) deleteMoreEventsButton();
        else if (moreEventsDeleted === true) putMoreEventsButton();
        var itemsToRemove = events.length < itemsAtPage ? events.length : itemsAtPage;
        appendEvents(events.splice(0, itemsToRemove));
        return;
    }
    events = process("filter", {
        date: [getTodayDate(), null],
        sort: "SORTBYTIME_ASC"
    });
    if(events.length == 0){
        clearEvents();
        eventArea.html('<h2 class="title title-modern text-center" style="padding-bottom: 15px;">No events found.</h2>');
        deleteMoreEventsButton();
        return; // no future event.
    }
    moreEvents();
}

function deleteMoreEventsButton() {
    document.getElementById('more-events').innerHTML = "";
    moreEventsDeleted = true;
}

function putMoreEventsButton() {
    if (moreEventsDeleted === true) {
        document.getElementById('more-events').innerHTML = moreEventsField;
    }
}

function appendEvents(events) {
    if (typeof events == 'string') events = [events];
    for (var i in events) {
        if (events.hasOwnProperty(i)) {
            var startDate = events[i]["start_time"].split(" ");
            var startHour = startDate[1].split(":");
            startHour = startHour[0] + ":" + startHour[1];
            startDate = parseDate(startDate[0]);

            var endDate = null;
            if (events[i]["end_time"] != null) {
                endDate = events[i]["end_time"].split(" ");
                var endHour = endDate[1].split(":");
                endHour = endHour[0] + ":" + endHour[1];
                endDate = parseDate(endDate[0]);
            }

            if (events[i]['cover'] == undefined || !events[i]['cover'].hasOwnProperty('source')) {
                events[i]['cover'] = {source: ""};
            }
            if (events[i]["place"] == undefined) {
                events[i]["place"] = {};
                if (!events[i]["place"].hasOwnProperty('name')) {
                    events[i]["place"] = {name: "Contact the host for information."};
                }
            }
            if (!events[i]["place"].hasOwnProperty("location") || !events[i]["place"]["location"].hasOwnProperty("latitude") || !events[i]["place"]["location"].hasOwnProperty("longitude")) {
                events[i]["place"]["location"] = {latitude: "", longitude: ""};
            }
            if (events[i]["ticket_uri"] == null) events[i]["ticket_uri"] = "https://www.facebook.com/events/" + events[i]["id"] + "/";

            var eventGridData = "<!-- event -->" +
                '<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><div class="card text-center" data-background="image">' +
                '<div style="background: url(' + events[i]["cover"]["source"] + ') repeat-x; background-size: cover; width:100%; height:100%; position:absolute; opacity: 0.6; -webkit-filter: blur(5px); -moz-filter: blur(5px); -o-filter: blur(5px); -ms-filter: blur(5px); filter: blur(5px);"></div>' + // blurry back bg
                '<div style="background-image: url(' + events[i]["cover"]["source"] + '); background-size: contain; background-position: 50% 50%; background-repeat:no-repeat; width:100%; height:100%; position:absolute;"></div>' + // front bg
                '<div class="header title title-modern text-left" style="margin-left:20px;">' + startDate + ' - ' + startHour + '</div>' +
                '<div class="footer btn-center">' +
                '<h4 class="title title-modern" style="margin-bottom:5px">' + events[i]["name"] + '</h4>' +
                '<h5 class="title title-modern" style="margin-top:0"><i style="opacity:0.8">by ' + events[i]["owner"].capitalize() + '</i></h5>' +
                '<a class="btn btn-neutral btn-round btn-modern" href="' + events[i]["ticket_uri"] + '" target="_blank">Get a Ticket</a>' +
                '<button class="btn btn-neutral btn-round btn-modern details" id="event' + eventCount + '" mapid="map' + eventCount + '" lat="' + events[i]["place"]["location"]["latitude"] + '" lng="' + events[i]["place"]["location"]["longitude"] + '" data-toggle="modal" data-target="#detail' + eventCount + '" style="margin-left:10px">&nbsp; Details &nbsp;</button>' +
                '</div>' +
                '<div class="filter filter-blue"></div>' +
                '</div></div>' +
                '<!-- end event -->';

            var modalData = '<!-- modal -->' +
                '<div class="modal modal-fullscreen fade" id="detail' + eventCount + '" tabindex="-1" role="dialog" aria-labelledby="label' + eventCount + '" aria-hidden="true">' +
                '<div class="modal-dialog">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
                '<h4 class="modal-title" id="label' + eventCount + '">' + events[i]["name"] + '</h4>' +
                '</div>' +
                '<div class="modal-body" style="display:inline-block">' +
                '<div class="col-xs-12 col-sm-12 col-md-5 col-lg-6" style="padding: 0; margin: 0;">' +
                '<h6 style="display: inline-block;">Date: </h6>' +
                '<small id="dateModalText"> ' + startDate + " " + startHour + (endDate != null ? ' - ' + endDate + " " + endHour : "") + '</small>' +
                '</div>' +
                '<div class="col-xs-12 col-sm-12 col-md-7 col-lg-6" style="padding: 0; margin: 0;">' +
                '<h6 style="display: inline-block;">Place: </h6>' +
                '<small id="placeModalText"> ' + events[i]["place"]["name"] + '</small>' +
                '</div>' +
                '<div class="col-xs-12 col-sm-12 col-md-5 col-lg-6" style="padding: 0; margin: 0;">' +
                '<h6 style="display: inline-block;">Host: </h6>' +
                '<small id="ownerModalText"> ' + events[i]["owner"] + '</small>' +
                '</div>' +
                '<div class="col-xs-12 col-sm-12 col-md-7 col-lg-6" style="padding: 0; margin: 0;">' +
                '<h6 style="display: inline-block;">Attending count: </h6>' +
                '<small id="attendingCountModalText"> ' + events[i]["attending_count"] + '</small>' +
                '</div>' +
                '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding: 0; margin: 10px 0 10px 0;">' +
                '<h6>Description</h6>' +
                '<hr style="margin-bottom: 10px"/><span style="white-space: pre-line;"><small>' + events[i]["description"] + '</small></span></div>' +
                '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding: 0; margin: 0;">' +
                '<h6>Map</h6>' +
                '<hr style="margin-bottom: 10px"/>' +
                '<div style="height: 300px;">' +
                '<div id="map' + eventCount + '" style="height: 100%"></div>' +
                '</div></div></div>' +
                '<div class="modal-footer">' +
                '<div class="left-side">' +
                '<a type="button" class="btn btn-default btn-simple" href="' + events[i]["ticket_uri"] + '" target="_blank">Get a Ticket</a>' +
                '</div>' +
                '<div class="divider"></div>' +
                '<div class="right-side">' +
                '<button type="button" class="btn btn-danger btn-simple" data-dismiss="modal">Close</button>' +
                '</div> </div> </div> </div> </div>' +
                '<!--   end modal -->';

            eventArea.append(eventGridData);
            modalArea.append(modalData);
            eventCount++;
        }
    }
}
var map = null;

function initMap(elID, lat, lng, title) {

    var myLatLng = {lat: lat, lng: lng};

    map = new google.maps.Map(document.getElementById(elID), {
        zoom: 14,
        center: myLatLng,
        scrollwheel: false
    });

    var marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
        title: title
    });

    var infowindow = new google.maps.InfoWindow({
        content: title
    });

    marker.addListener('click', function () {
        infowindow.open(map, marker);
    });


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
    today.setDate(today.getDate() + 1);
    dd = today.getDate();
    mm = today.getMonth() + 1;
    yyyy = today.getFullYear();
    if (datedd == dd && datemm == mm && dateyyyy == yyyy) return "Tomorrow";
    return datedd + DATE_SEPARATOR + datemm + DATE_SEPARATOR + dateyyyy;
}

$(document).ready(function () {
    var clubDivider = $('ul[aria-labelledby="clubs"]').find('li[class="divider"]');
    var catDivider = $('ul[aria-labelledby="categories"]').find('li[class="divider"]');
    var i1 = 3;
    var clubs = process("getClubs", null);
    for (var i = clubs.length - 1; i > -1; i--) {
        if (clubs.hasOwnProperty(i))
            clubDivider.after('<li class="single"><label class="checkbox" for="checkbox' + i1 + '" style="margin-left: 5%"><span class="icons"><span class="first-icon fa fa-square fa-base"></span><span class="second-icon fa fa-check-square fa-base"></span></span><input class="club" type="checkbox" value="' + clubs[i][0] + '" id="checkbox' + (i1++) + '" data-toggle="checkbox">' + clubs[i][1] + '</label></li>');
    }

    var categories = process("getCategories", null);
    for (i = categories.length - 1; i > -1; i--) {
        if (categories.hasOwnProperty(i))
            catDivider.after('<li class="single"><label class="checkbox" for="checkbox' + i1 + '" style="margin-left: 5%"><span class="icons"><span class="first-icon fa fa-square fa-base"></span><span class="second-icon fa fa-check-square fa-base"></span></span><input class="category" type="checkbox" value="' + categories[i] + '" id="checkbox' + (i1++) + '" data-toggle="checkbox">' + categories[i].capitalize() + '</label></li>');
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

    $("#filterToggle").on("click", function () {
        $("#filterbox").toggleClass("hidden");
    });

    $("#filter").on("click", function () {
        var s = $('#start_time').val().split("/").reverse().join("/");
        var e = $('#end_time').val().split("/").reverse().join("/");
        events = process("filter", {
            clubs: checkedClubs,
            categories: checkedCategories,
            date: [s, e],
            sort: "SORTBYTIME_ASC"
        });
        putEvents();
    });

    $("#search").on("click", function () {
        search();
    });

    $("#searchField").keydown(function (e) {
        if (e.keyCode == 13)
            search();
    });

    checkClub("All clubs", "All clubs");
    checkCategory("All categories");

    moreEvents(); // initialize first events

    eventArea.on("click", ".details", function () {
        var lat = parseFloat($(this).attr("lat"));
        var lng = parseFloat($(this).attr("lng"));
        //var title = $(this).parent().find("h4.title").html();

        if (isNaN(lat) || isNaN(lng)) {
            var mapField = $("#" + $(this).attr("mapid"));
            mapField.html("<h6>No location data.</h6>");
            mapField.parent().css("height", "50px");
        } else {
            initMap($(this).attr("mapid"), lat, lng, "Event place");
            google.maps.event.addListenerOnce(map, 'idle', function () {
                google.maps.event.trigger(map, 'resize');
                map.setCenter({lat: lat, lng: lng});
            });
        }
    });

});

function search() {
    var searchVal = $("#searchField").val();
    if (searchVal.length !== 0) {
        events = process("search", {
            val: searchVal,
            sort: "SORTBYTIME_DESC"
        });
        putEvents();
    } else {
        events = [];
        clearEvents();
        moreEvents();
    }
}

function putEvents() {
    if (events === undefined || events.length == 0) {
        eventArea.html('<h2 class="title title-modern text-center" style="padding-bottom: 15px;">No events found.</h2>');
        deleteMoreEventsButton();
    } else {
        clearEvents();
        moreEvents();
    }
}

