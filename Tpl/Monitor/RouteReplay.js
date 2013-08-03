// JScript source code
var RoutePoints;
var ReplayArray = null; // 所有要回放的点都要放到这个数组里面，之后的回放操作从该数组中获取回放点
var bStopReplay = true;


function requestRest_getRoutePoints() {
    StopMonitoring();
    if (0 == validateControlContent(1) && 0 == validateControlContent(2))//保证输入的时间格式正确
    {
        var strCarID = $("#InputeCarID")[0].value;
        var strUrl = ComposeGetRoutePointsUrl(strStartTime, strEndTime, strCarID);
        $.ajax({
            type: "get",
            //url: "http://localhost/SvcFiles/GetPointsRestful.svc/GetRouteP/270001/2008-05-10/2008-05-14",
            url: strUrl,
            success: function(data, textStatus) {
                RoutePointsReturn(data);
            },
            complete: function(XMLHttpRequest, textStatus) {
                //HideLoading();
            },
            error: function() {
                //
            }
        });
    }

}

function RoutePointsReturn(data) {
    RoutePoints = data;
    var pointsCount = RoutePoints.length;
    if (pointsCount <= 0) {
        alert("没有返回路线！");
        return;
    }
    //var Routepoint1 = RoutePoints[0];
    if (ReplayArray == null) {
        ReplayArray = new Array();
    }
    else {
        ReplayArray.length = 0;
    }
    for (var j = 0; j < pointsCount; j++) {
        ReplayArray[j] = new CarRoutePoint(RoutePoints[j].StrCarID, RoutePoints[j].StrLatitude, RoutePoints[j].StrLongitude, RoutePoints[j].StrTime);
    }
    if (bStopReplay == false) {
        bStopReplay = true;
    }
    else {
        bStopReplay = false;
        timer_SetPointMarkOnMap();
    }

}

function CarRoutePoint(id, lat, lng, time) {
    this.carID = id;
    this.lat = lat;
    this.lng = lng;
    this.timeStump = time;
}
function timer_SetPointMarkOnMap() {
    if (iTimeElapse >= ReplayArray.length) {
        //if (iTimeElapse >= RoutePoints.length) {
        // 清楚所有路线经过点的地图标记
        ClearRoutePointsMarkOnMap();
        iTimeElapse = 0;
    }
    var carRoutePoint = ReplayArray[iTimeElapse];
    if (null != carRoutePoint) {
        var gLatLngPoint = new google.maps.LatLng(carRoutePoint.lat / 3600000, carRoutePoint.lng / 3600000);
        //var RoutePoint = RoutePoints[iTimeElapse];
        //var gLatLngPoint = new google.maps.LatLng(RoutePoint.StrLatitude / 3600000, RoutePoint.StrLongitude / 3600000);
        AddRoutePointsMarkOnMap(gLatLngPoint, iTimeElapse);
        RouteReplay_SetPointMarkOnMap(gLatLngPoint);
        if (bStopReplay == false) {
            iTimeElapse++;
            setTimeout(timer_SetPointMarkOnMap, 1000);
        }
    }
}
function ClearRoutePointsMarkOnMap() {
    if (g_markArray != null) {
        for (var i = 0; i < g_markArray.length; i++) {
            if (g_markArray[i] != null) {
                g_markArray[i].setMap(null);
            }
        }
    }
}
function AddRoutePointsMarkOnMap(Point, index) {
    if (null == g_markArray) {
        g_markArray = new Array();
    }
    var pos = Point;
    if (bStopReplay == false) {
        g_markArray[index] = new google.maps.Marker(
			    { position: pos,
			        map: g_map,
			        icon: "red.png"
			    });
    }
}
function RouteReplay_SetPointMarkOnMap(Point) {
    //var pos = new google.maps.LatLng(Point.StrLatitude,Point.StrLongitude);
    var pos = Point;
    //{lat:Point.StrLatitude,lng:Point.StrLongitude};
    if (g_mark != null) {
        g_mark.setMap(null); // delete the mark
    }
    if (bStopReplay == false) {
        g_mark = new google.maps.Marker(
			    { position: pos,
			        map: g_map,
			        icon: "car2.gif"
			    });
        g_map.setCenter(pos);
    }
}
function StopRouteReplay() {
    bStopReplay = true;
    if (g_mark != null) {
        g_mark.setMap(null); // delete the mark
    }
    ClearRoutePointsMarkOnMap();
}
//function StartReplay() {
//    //SetReplayPoints();
//    //var Point = LatlngArray[iTimeElapse];
//    var RoutePoint = RoutePoints[iTimeElapse];
//    var Point = new google.maps.LatLng(RoutePoint.StrLatitude, RoutePoint.StrLongitude);
//    g_mark = new google.maps.Marker(
//			    { position: Point,
//			        map: g_map,
//			        icon: "car2.gif"
//			    });
//    g_map.setCenter(Point);
//    iTimeElapse = 0;
//    SetPointMarkOnMap();
//}