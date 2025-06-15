<?php
use Proxy\Api\Api;
require_once __DIR__ . '/proxy/api.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/functions.php';
Api::__constructStatic();
session_start();
if (isset($_SESSION['pilotid'])) {
    $pilot = null;
    $res = Api::sendAsync('GET', 'v1/pilot/location', null);
    if ($res->getStatusCode() == 200) {
        $pilot = json_decode($res->getBody());
    }
}
$schedule = null;
$res = Api::sendSync('GET', 'v1/operations/schedule/routes', null);
if ($res->getStatusCode() == 200) {
    $schedule = json_decode($res->getBody(), true);
}
if (!empty($schedule)) {
    foreach ($schedule as &$flight) {
        $flight["schedule"]["flightNumber"] = '<i class="fa fa-plane"></i> <a href="' . website_base_url . 'flight_info.php?id=' . $flight["schedule"]['id'] . '" target="_blank">' . $flight["schedule"]["flightNumber"] . '</a>';
    }
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php include 'includes/header.php';?>
<link rel="stylesheet" type="text/css" href="assets/plugins/datatables/datatables.min.css" />
<link rel="stylesheet" href="<?php echo website_base_url; ?>assets/plugins/leaflet/leaflet.css" />
<script src="<?php echo website_base_url; ?>assets/plugins/leaflet/leaflet.js"></script>
<script src="<?php echo website_base_url; ?>assets/plugins/leaflet/arc.js" type="text/javascript"></script>
<script src="<?php echo website_base_url; ?>assets/plugins/leaflet/map.helpers.js" type="text/javascript"></script>
<style>
#map {
    height: 600px;
    margin-bottom: 20px;
}

.find-msg {
    color: red;
    display: block;
    font-size: 12px;
}

.icao {
    width: 120px;
    height: 38px;
    display: inline;
    text-transform: uppercase;
}
</style>
<section id="content" class="cp section offset-header">
    <div class="container">
        <div class="row">
            <div class="jumbotron text-center">
                <h1>Route Map</h1>
                <hr />
                <p>Select a location on the map to view flights to and from the airport. You can select the airport by
                    clicking on your location below, or by searching for an ICAO. Alternatively, you can use the <a
                        href="flight_search.php">advanced schedule</a> search.</p>
                <p><i class="fa fa-map-marker" aria-hidden="true"></i> View flights from your current location:
                    <strong><?php echo empty($pilot->location) ? "N/A" : '<a href="#"><i class="fa fa-external-link"></i> <span class="curlocation">' . $pilot->location . '</span></a>'; ?></strong>
                </p>
                <input type="text" maxlength="4" name="icao" id="icao" class="form-control icao" placeholder="ICAO" />
                <input name="find" onclick="find()" type="submit" id="find" value="Find" class="btn btn-default">
                <span class="find-msg"></span>
            </div>
            <div id="map"></div>
        </div>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Available Flights</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped" id="flights" width="100%">
                        <thead>
                            <tr>
                                <th>Flight<br />Number</th>
                                <th>Departure<br />ICAO</th>
                                <th>Arrival<br />ICAO</th>
                                <th>Depart<br />UTC</th>
                                <th>Arrive<br />UTC</th>
                                <th>Duration</th>
                                <th>Operator</th>
                                <th>Aircraft</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php';?>
<script type="text/javascript" src="assets/plugins/datatables/datatables.min.js"></script>
<script type="text/javascript">
var schedule = <?php echo json_encode($schedule); ?>;
var _0x223b8c = _0x3ff3;
(function(_0x2d0c35, _0x1622ca) {
    var _0x23e329 = _0x3ff3,
        _0x83cd32 = _0x2d0c35();
    while (!![]) {
        try {
            var _0x20624f = parseInt(_0x23e329(0x1d9)) / 0x1 + -parseInt(_0x23e329(0x1e5)) / 0x2 + parseInt(
                    _0x23e329(0x1de)) / 0x3 * (-parseInt(_0x23e329(0x1e4)) / 0x4) + -parseInt(_0x23e329(0x1fc)) /
                0x5 * (-parseInt(_0x23e329(0x1f1)) / 0x6) + -parseInt(_0x23e329(0x204)) / 0x7 * (parseInt(_0x23e329(
                    0x200)) / 0x8) + parseInt(_0x23e329(0x1fe)) / 0x9 + -parseInt(_0x23e329(0x1ef)) / 0xa * (-
                    parseInt(_0x23e329(0x1c9)) / 0xb);
            if (_0x20624f === _0x1622ca) break;
            else _0x83cd32['push'](_0x83cd32['shift']());
        } catch (_0x20932f) {
            _0x83cd32['push'](_0x83cd32['shift']());
        }
    }
}(_0x52bc, 0x2e799));

function _0x3ff3(_0x1d6be2, _0x1f1ab9) {
    var _0x52bc76 = _0x52bc();
    return _0x3ff3 = function(_0x3ff38c, _0x60cbc0) {
        _0x3ff38c = _0x3ff38c - 0x1c6;
        var _0x3026b3 = _0x52bc76[_0x3ff38c];
        return _0x3026b3;
    }, _0x3ff3(_0x1d6be2, _0x1f1ab9);
}
var map, dataTable, dataSet = [],
    airports = [];
$(document)[_0x223b8c(0x1cc)](function() {
    var _0x7503e7 = _0x223b8c;
    map = L[_0x7503e7(0x1e7)](_0x7503e7(0x1e7), {
        'center': [0x0, 0x0],
        'zoom': 0x2,
        'worldCopyJump': !![],
        'attributionControl': ![]
    }), L[_0x7503e7(0x1e9)](_0x7503e7(0x20f), {
        'maxZoom': 0x13,
        'attribution': ''
    })[_0x7503e7(0x1eb)](map), loadDestinations(), initFlightTable(), $(_0x7503e7(0x1e3))['on'](_0x7503e7(
        0x1fa), function(_0x14647b) {
        var _0x57a1d8 = _0x7503e7;
        _0x14647b[_0x57a1d8(0x208)](), removeActiveMarker();
        var _0x10ca46 = map[_0x57a1d8(0x1d0)]($(this)[_0x57a1d8(0x1c8)]());
        _0x10ca46 != null && _0x10ca46[_0x57a1d8(0x211)](_0x57a1d8(0x1fa));
    });
});

function find() {
    var _0xf9e37f = _0x223b8c;
    $('.find-msg')[_0xf9e37f(0x1c8)](''), removeActiveMarker();
    var _0x942d09 = map[_0xf9e37f(0x1d0)]($(_0xf9e37f(0x205))['val']()['toUpperCase']());
    _0x942d09 != null ? _0x942d09['fire']('click') : $(_0xf9e37f(0x209))[_0xf9e37f(0x1c8)]('Location\x20not\x20found.');
}

function removeActiveMarker() {
    var _0x40b439 = _0x223b8c;
    map[_0x40b439(0x1ec)](function(_0x764055) {
        var _0x28afaf = _0x40b439;
        _0x764055 instanceof L['Marker'] && (marker = _0x764055, marker[_0x28afaf(0x1fb)][_0x28afaf(0x1e0)](
            marker[_0x28afaf(0x1cf)]) && marker['fire'](_0x28afaf(0x1d4)));
    });
}

function _0x52bc() {
    var _0x36c543 = ['2091480JgjSqJ', '<p><strong\x20style=\x22color:black;\x20font-size:12px\x22>', '159876hzPfDw',
        'Select\x20a\x20location\x20on\x20the\x20map\x20to\x20list\x20flights\x20to\x20and\x20from\x20the\x20airport.',
        'arrIcao', 'DataTable', 'duration', 'removeLayer', 'coords', 'airportInfo', 'find', 'click', '_map',
        '60ynOkyd', 'depIcao', '315747MZWTPN', '#flights', '2033432tKZAPg', 'operator', 'polyline', 'geometries',
        '7XUYAdf', '#icao', 'closePopup', 'schedule', 'preventDefault', '.find-msg', 'asc', 'data', 'getPopup',
        '<div\x20class=\x22label_content\x22><span>', '</span></div>',
        'https://tile.openstreetmap.org/{z}/{x}/{y}.png', 'name', 'fire', 'setContent',
        '<div\x20class=\x22label_content\x22><span\x20title=\x22', 'html', '11ngdZdV', 'Arc', 'popup', 'ready',
        'push', 'destroy', '_popup', 'getMarkerById', 'lng', 'getFeatureGroupById', 'red', 'remove', 'openPopup',
        'getBounds', 'iconicon', 'filter', '65905WVmJVl', 'fitBounds', 'depTime', 'icao', 'featureGroup',
        '411nUgBeh', 'aircraftTypeCommas', 'hasLayer', 'GreatCircle', 'mouseout', '.curlocation', '1252WwWFlx',
        '284934VSsRrm', 'marker', 'map', 'bindPopup', 'tileLayer', 'divIcon', 'addTo', 'eachLayer', '</strong></p>',
        'lat'
    ];
    _0x52bc = function() {
        return _0x36c543;
    };
    return _0x52bc();
}

function exists(_0x336a85) {
    var _0x3873ec = _0x223b8c,
        _0x8a933c, _0xcba012 = 0x0;
    while (_0x8a933c = airports[_0xcba012++])
        if (_0x8a933c[_0x3873ec(0x1dc)] == _0x336a85) return --_0xcba012;
    return -0x1;
}

function loadDestinations() {
    var _0x29c37d = _0x223b8c,
        _0x6888eb = L[_0x29c37d(0x1dd)]()[_0x29c37d(0x1eb)](map);
    _0x6888eb['id'] = 0x1;
    if (airports['length'] < 0x1)
        for (var _0x2306ee in schedule) {
            exists(schedule[_0x2306ee][_0x29c37d(0x1f8)][0x0][_0x29c37d(0x1dc)]) < 0x0 && airports[_0x29c37d(0x1cd)](
                schedule[_0x2306ee][_0x29c37d(0x1f8)][0x0]), exists(schedule[_0x2306ee][_0x29c37d(0x1f8)][0x1][
                _0x29c37d(0x1dc)
            ]) < 0x0 && airports[_0x29c37d(0x1cd)](schedule[_0x2306ee][_0x29c37d(0x1f8)][0x1]);
        }
    for (var _0x2306ee in airports) {
        var _0x43bcb8 = L[_0x29c37d(0x1cb)]({
            'offset': [0x0, -0x14]
        })[_0x29c37d(0x1c6)]('<p><strong\x20style=\x22color:black;\x20font-size:12px\x22>' + airports[_0x2306ee][
            _0x29c37d(0x210)
        ] + _0x29c37d(0x1ed));
        marker = new L[(_0x29c37d(0x1e6))]([airports[_0x2306ee][_0x29c37d(0x1ee)], airports[_0x2306ee][_0x29c37d(
                0x1d1)]], {
                'icon': L[_0x29c37d(0x1ea)]({
                    'className': _0x29c37d(0x1d7),
                    'html': '<div\x20class=\x22label_content\x22><span>' + airports[_0x2306ee][_0x29c37d(
                        0x1dc)] + _0x29c37d(0x20e),
                    'iconAnchor': [0x14, 0x1e]
                })
            })['on'](_0x29c37d(0x1fa), markerClick)[_0x29c37d(0x1eb)](_0x6888eb), marker[_0x29c37d(0x1e8)](_0x43bcb8),
            marker['on']('mouseover', function(_0xf42444) {
                var _0x396144 = _0x29c37d;
                this[_0x396144(0x1d5)]();
            }), marker['on'](_0x29c37d(0x1e2), function(_0xee1791) {
                var _0x3e0e6a = _0x29c37d;
                this[_0x3e0e6a(0x206)]();
            }), marker['acarsId'] = airports[_0x2306ee][_0x29c37d(0x1dc)], marker[_0x29c37d(0x1dc)] = airports[
                _0x2306ee][_0x29c37d(0x1dc)], marker[_0x29c37d(0x20b)] = airports[_0x2306ee];
    }
    map[_0x29c37d(0x1da)](_0x6888eb[_0x29c37d(0x1d6)]());
}

function clearDestinations() {
    var _0x311add = _0x223b8c,
        _0x3dc3d3 = map[_0x311add(0x1d2)](0x1);
    _0x3dc3d3 != null && map[_0x311add(0x1f6)](_0x3dc3d3);
}

function clearDestinationFlights() {
    var _0x55f69c = _0x223b8c,
        _0x510986 = map[_0x55f69c(0x1d2)](0x2);
    _0x510986 != null && map[_0x55f69c(0x1f6)](_0x510986);
}

function markerPopupClose(_0x234d2c) {
    clearDestinations(), clearDestinationFlights(), loadDestinations(), dataSet = [], initFlightTable();
}

function markerClick(_0x159cd7) {
    var _0xb613d0 = _0x223b8c,
        _0x27f731 = L[_0xb613d0(0x1dd)]();
    _0x27f731['id'] = 0x2;
    var _0x1b2355 = this['data'];
    clearDestinations();
    var _0x5682b8 = L[_0xb613d0(0x1cb)]({
        'offset': [0x0, -0x14]
    })[_0xb613d0(0x1c6)](_0xb613d0(0x1f0) + _0x1b2355[_0xb613d0(0x210)] + _0xb613d0(0x1ed));
    marker = new L[(_0xb613d0(0x1e6))]([_0x1b2355[_0xb613d0(0x1ee)], _0x1b2355[_0xb613d0(0x1d1)]], {
        'icon': L[_0xb613d0(0x1ea)]({
            'className': 'iconicon',
            'html': _0xb613d0(0x20d) + _0x1b2355['icao'] + _0xb613d0(0x20e),
            'iconAnchor': [0x14, 0x1e]
        })
    })[_0xb613d0(0x1eb)](_0x27f731), marker['bindPopup'](_0x5682b8), marker[_0xb613d0(0x20c)]()['on']('remove',
        markerPopupClose), _0x27f731[_0xb613d0(0x1eb)](map), marker[_0xb613d0(0x1d5)]();
    var _0x54cd1c = schedule;
    _0x54cd1c = _0x54cd1c[_0xb613d0(0x1d8)](function(_0x3dd3f2) {
        var _0x26150e = _0xb613d0;
        return _0x3dd3f2[_0x26150e(0x207)][_0x26150e(0x1fd)] == _0x1b2355[_0x26150e(0x1dc)]['toUpperCase']() ||
            _0x3dd3f2[_0x26150e(0x207)][_0x26150e(0x1f3)] == _0x1b2355['icao']['toUpperCase']();
    }), dataSet = [];
    for (var _0x281115 in _0x54cd1c) {
        dataSet[_0xb613d0(0x1cd)](_0x54cd1c[_0x281115][_0xb613d0(0x207)]);
        var _0x309fe8 = _0x54cd1c[_0x281115][_0xb613d0(0x1f8)][_0xb613d0(0x1f9)](_0x3d9b79 => _0x3d9b79[_0xb613d0(
                0x1dc)] != _0x1b2355['icao']),
            _0x3a2e15 = {
                'x': _0x1b2355[_0xb613d0(0x1d1)],
                'y': _0x1b2355[_0xb613d0(0x1ee)]
            };
        if (_0x309fe8 != null) {
            var _0x5df0e4 = {
                    'x': _0x309fe8['lng'],
                    'y': _0x309fe8[_0xb613d0(0x1ee)]
                },
                _0x4f7dab = new arc[(_0xb613d0(0x1e1))](_0x3a2e15, _0x5df0e4),
                _0x25c639 = _0x4f7dab[_0xb613d0(0x1ca)](0x64, {
                    'offset': 0xa
                }),
                _0x4df4a5 = [];
            for (var _0x281115 in _0x25c639[_0xb613d0(0x203)][0x0][_0xb613d0(0x1f7)]) {
                _0x4df4a5[_0xb613d0(0x1cd)]([_0x25c639['geometries'][0x0][_0xb613d0(0x1f7)][_0x281115][0x1], _0x25c639[
                    _0xb613d0(0x203)][0x0][_0xb613d0(0x1f7)][_0x281115][0x0]]);
            }
            var _0x54cf3c = L[_0xb613d0(0x202)](_0x4df4a5, {
                'color': _0xb613d0(0x1d3),
                'weight': 0x2,
                'dashArray': [0x5, 0x5]
            })['addTo'](_0x27f731);
            marker = new L[(_0xb613d0(0x1e6))]([_0x309fe8['lat'], _0x309fe8[_0xb613d0(0x1d1)]], {
                'icon': L[_0xb613d0(0x1ea)]({
                    'className': 'iconicon',
                    'html': _0xb613d0(0x1c7) + _0x309fe8[_0xb613d0(0x210)] + '\x22>' + _0x309fe8[
                        'icao'] + _0xb613d0(0x20e),
                    'iconAnchor': [0x14, 0x1e]
                })
            })[_0xb613d0(0x1eb)](_0x27f731);
        }
        _0x27f731[_0xb613d0(0x1eb)](map);
    }
    initFlightTable(), _0x27f731[_0xb613d0(0x1eb)](map), map[_0xb613d0(0x1da)](_0x27f731['getBounds']());
}

function initFlightTable() {
    var _0x4ef555 = _0x223b8c;
    dataTable != null && dataTable[_0x4ef555(0x1ce)](), dataTable = $(_0x4ef555(0x1ff))[_0x4ef555(0x1f4)]({
        'initComplete': function() {},
        'data': dataSet,
        'processing': ![],
        'serverSide': ![],
        'language': {
            'sEmptyTable': _0x4ef555(0x1f2)
        },
        'pageLength': 0x32,
        'columns': [{
            'data': 'flightNumber',
            'orderable': ![]
        }, {
            'data': _0x4ef555(0x1fd)
        }, {
            'data': _0x4ef555(0x1f3)
        }, {
            'data': _0x4ef555(0x1db),
            'orderable': ![]
        }, {
            'data': 'arrTime',
            'orderable': ![]
        }, {
            'data': _0x4ef555(0x1f5)
        }, {
            'data': _0x4ef555(0x201)
        }, {
            'data': _0x4ef555(0x1df),
            'orderable': ![]
        }],
        'colReorder': ![],
        'order': [
            [0x7, _0x4ef555(0x20a)]
        ]
    });
}
</script>