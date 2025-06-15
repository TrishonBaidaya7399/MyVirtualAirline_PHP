<?php
use Proxy\Api\Api;
require_once __DIR__ . '/../proxy/api.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/functions.php';
Api::__constructStatic();
session_start();
validateSession();
$id = $_SESSION['pilotid'];
$logbook = null;
$res = Api::sendSync('GET', 'v1/pilot/logbook/map/' . $id, null);
if ($res->getStatusCode() == 200) {
    $logbook = json_decode($res->getBody(), true);
}
if (!empty($logbook)) {
    foreach ($logbook as &$item) {
        $item["logbookItem"]["date"] = (new DateTime($item["logbookItem"]["date"]))->format('Y-m-d');
        $item["logbookItem"]["aircraft"] = '<span title="' . $item["logbookItem"]["aircraft"] . '">' . limit($item["logbookItem"]["aircraft"], 15, "...") . '</span>';
        $item["logbookItem"]["flightNumber"] = '<a href="' . website_base_url . 'pirep_info.php?id=' . $item["logbookItem"]['id'] . '" target="_blank">' . $item["logbookItem"]["flightNumber"] . '</a>';
        switch ($item["logbookItem"]["approvedStatus"]) {
            case 0:
                $item["logbookItem"]["approvedStatusDescription"] = "<span style='color:orange;'>Pending Approval</span>";
                break;
            case 1:
                $item["logbookItem"]["approvedStatusDescription"] = "<span style='color:green;'>Approved</span>";
                break;
            case 2:
                $item["logbookItem"]["approvedStatusDescription"] = "<span style='color:red;'>Denied</span>";
                break;
        }
    }
}
?>
<?php
$MetaPageTitle = "";
$MetaPageDescription = "";
$MetaPageKeywords = "";
?>
<?php include '../includes/header.php';?>
<link rel="stylesheet" type="text/css"
    href="<?php echo website_base_url; ?>assets/plugins/datatables/datatables.min.css" />
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
                <h1>Logbook Map</h1>
                <hr />

                <p>Below is a map of all the airports you have visited. Select a location on the map to view your
                    previous flights to and from the airport. You can also
                    search previous flights by ICAO.</p>
                <input type="text" maxlength="4" name="icao" id="icao" class="form-control icao" placeholder="ICAO" />
                <input name="find" onclick="find()" type="submit" id="find" value="Find" class="btn btn-default">
                <span class="find-msg"></span>
            </div>
            <div id="map"></div>
        </div>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Previous Flights</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-striped" id="flights" width="100%">
                        <thead>
                            <tr>
                                <th><strong>Date</strong></th>
                                <th><strong>Flight No.</strong></th>
                                <th><strong>Depart</strong></th>
                                <th><strong>Arrive</strong></th>
                                <th><strong>Duration</strong></th>
                                <th><strong>Aircraft</strong></th>
                                <th><strong>Status</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include '../includes/footer.php';?>
<script type="text/javascript" src="<?php echo website_base_url; ?>assets/plugins/datatables/datatables.min.js">
</script>
<script type="text/javascript">
var logbook = <?php echo json_encode($logbook); ?>;
var _0x277a74 = _0x4dc7;

function _0x4dc7(_0x336281, _0x31db0f) {
    var _0x994138 = _0x9941();
    return _0x4dc7 = function(_0x4dc7a6, _0x330a97) {
        _0x4dc7a6 = _0x4dc7a6 - 0x185;
        var _0x15f26b = _0x994138[_0x4dc7a6];
        return _0x15f26b;
    }, _0x4dc7(_0x336281, _0x31db0f);
}(function(_0x58cd4b, _0x3a562f) {
    var _0x2cba4e = _0x4dc7,
        _0x585085 = _0x58cd4b();
    while (!![]) {
        try {
            var _0x509d93 = -parseInt(_0x2cba4e(0x18a)) / 0x1 + parseInt(_0x2cba4e(0x1a2)) / 0x2 * (-parseInt(
                    _0x2cba4e(0x1ca)) / 0x3) + parseInt(_0x2cba4e(0x1ad)) / 0x4 + -parseInt(_0x2cba4e(0x19e)) /
                0x5 * (parseInt(_0x2cba4e(0x1b8)) / 0x6) + parseInt(_0x2cba4e(0x1a4)) / 0x7 * (parseInt(_0x2cba4e(
                    0x199)) / 0x8) + parseInt(_0x2cba4e(0x19f)) / 0x9 * (-parseInt(_0x2cba4e(0x1c5)) / 0xa) +
                parseInt(_0x2cba4e(0x19d)) / 0xb;
            if (_0x509d93 === _0x3a562f) break;
            else _0x585085['push'](_0x585085['shift']());
        } catch (_0x31122d) {
            _0x585085['push'](_0x585085['shift']());
        }
    }
}(_0x9941, 0xe3e1b));

function _0x9941() {
    var _0x340692 = ['ready', 'length', 'fitBounds', 'Location\x20not\x20found.', 'val', '</strong></p>', '29896kzPtdh',
        'removeLayer', 'featureGroup', '.find-msg', '36462690sccRom', '959970IFOTEV', '3249UlwKpR', 'filter', 'red',
        '178mCCeRY', 'mouseout', '791MoHaTT', 'polyline', 'lng', 'airportInfo', 'remove', 'html',
        '<p><strong\x20style=\x22color:black;\x20font-size:12px\x22>', 'bindPopup', 'setContent', '1072104WHwIFf',
        'destroy', 'map', 'divIcon', '</span></div>', 'push', 'find', 'geometries',
        'https://tile.openstreetmap.org/{z}/{x}/{y}.png', 'name', 'click', '48VYhDQc', 'GreatCircle', 'eachLayer',
        'arrIcao', 'DataTable', 'flightNumber', 'Arc', '_popup', 'toUpperCase', 'fire', 'popup', 'getMarkerById',
        'mouseover', '3070wriwMf', '<div\x20class=\x22label_content\x22><span>', 'date', 'hasLayer', 'data',
        '9864yUttKf', 'addTo', 'logbookItem', 'aircraft', 'getFeatureGroupById', 'openPopup', 'getBounds',
        'depIcao', 'Marker', '#icao', 'marker', '1132283ddMHtP', 'iconicon', 'coords', 'icao', '_map', 'closePopup',
        'lat', 'acarsId', 'tileLayer'
    ];
    _0x9941 = function() {
        return _0x340692;
    };
    return _0x9941();
}
var map, dataTable, dataSet = [],
    airports = [];
$(document)[_0x277a74(0x193)](function() {
    var _0x42afd3 = _0x277a74;
    map = L[_0x42afd3(0x1af)](_0x42afd3(0x1af), {
        'center': [0x0, 0x0],
        'zoom': 0x2,
        'worldCopyJump': !![],
        'attributionControl': ![]
    }), L[_0x42afd3(0x192)](_0x42afd3(0x1b5), {
        'maxZoom': 0x13,
        'attribution': ''
    })[_0x42afd3(0x1cb)](map), loadDestinations(), initFlightTable();
});

function find() {
    var _0x2df189 = _0x277a74;
    $(_0x2df189(0x19c))[_0x2df189(0x1a9)](''), removeActiveMarker();
    var _0x138f24 = map[_0x2df189(0x1c3)]($(_0x2df189(0x188))[_0x2df189(0x197)]()[_0x2df189(0x1c0)]());
    _0x138f24 != null ? _0x138f24[_0x2df189(0x1c1)](_0x2df189(0x1b7)) : $(_0x2df189(0x19c))[_0x2df189(0x1a9)](_0x2df189(
        0x196));
}

function removeActiveMarker() {
    var _0x47cd76 = _0x277a74;
    map[_0x47cd76(0x1ba)](function(_0x4e5b03) {
        var _0x4b8162 = _0x47cd76;
        _0x4e5b03 instanceof L[_0x4b8162(0x187)] && (marker = _0x4e5b03, marker[_0x4b8162(0x18e)][_0x4b8162(
            0x1c8)](marker[_0x4b8162(0x1bf)]) && marker[_0x4b8162(0x1c1)](_0x4b8162(0x1a8)));
    });
}

function exists(_0x23e165) {
    var _0x1eb988 = _0x277a74,
        _0x2b2c45, _0x54b3d6 = 0x0;
    while (_0x2b2c45 = airports[_0x54b3d6++])
        if (_0x2b2c45[_0x1eb988(0x18d)] == _0x23e165) return --_0x54b3d6;
    return -0x1;
}

function loadDestinations() {
    var _0xca68fd = _0x277a74,
        _0x423d36 = L[_0xca68fd(0x19b)]()[_0xca68fd(0x1cb)](map);
    _0x423d36['id'] = 0x1;
    if (airports[_0xca68fd(0x194)] < 0x1)
        for (var _0x4a4c6c in logbook) {
            exists(logbook[_0x4a4c6c][_0xca68fd(0x1a7)][0x0][_0xca68fd(0x18d)]) < 0x0 && airports[_0xca68fd(0x1b2)](
                logbook[_0x4a4c6c]['airportInfo'][0x0]), exists(logbook[_0x4a4c6c]['airportInfo'][0x1][_0xca68fd(
                0x18d)]) < 0x0 && airports[_0xca68fd(0x1b2)](logbook[_0x4a4c6c][_0xca68fd(0x1a7)][0x1]);
        }
    for (var _0x4a4c6c in airports) {
        var _0x49736d = L[_0xca68fd(0x1c2)]({
            'offset': [0x0, -0x14]
        })[_0xca68fd(0x1ac)](_0xca68fd(0x1aa) + airports[_0x4a4c6c][_0xca68fd(0x1b6)] + _0xca68fd(0x198));
        marker = new L[(_0xca68fd(0x189))]([airports[_0x4a4c6c][_0xca68fd(0x190)], airports[_0x4a4c6c][_0xca68fd(
                0x1a6)]], {
                'icon': L['divIcon']({
                    'className': _0xca68fd(0x18b),
                    'html': _0xca68fd(0x1c6) + airports[_0x4a4c6c][_0xca68fd(0x18d)] + _0xca68fd(0x1b1),
                    'iconAnchor': [0x14, 0x1e]
                })
            })['on'](_0xca68fd(0x1b7), markerClick)[_0xca68fd(0x1cb)](_0x423d36), marker['bindPopup'](_0x49736d),
            marker['on'](_0xca68fd(0x1c4), function(_0x1cefbb) {
                this['openPopup']();
            }), marker['on'](_0xca68fd(0x1a3), function(_0x25cc60) {
                var _0x51d82f = _0xca68fd;
                this[_0x51d82f(0x18f)]();
            }), marker[_0xca68fd(0x191)] = airports[_0x4a4c6c]['icao'], marker[_0xca68fd(0x18d)] = airports[_0x4a4c6c][
                _0xca68fd(0x18d)
            ], marker[_0xca68fd(0x1c9)] = airports[_0x4a4c6c];
    }
    map[_0xca68fd(0x195)](_0x423d36[_0xca68fd(0x185)]());
}

function clearDestinations() {
    var _0x5d3627 = _0x277a74,
        _0x326891 = map[_0x5d3627(0x1ce)](0x1);
    _0x326891 != null && map[_0x5d3627(0x19a)](_0x326891);
}

function clearDestinationFlights() {
    var _0x1ff307 = map['getFeatureGroupById'](0x2);
    _0x1ff307 != null && map['removeLayer'](_0x1ff307);
}

function markerPopupClose(_0x69d191) {
    clearDestinations(), clearDestinationFlights(), loadDestinations(), dataSet = [], initFlightTable();
}

function markerClick(_0xc4ea24) {
    var _0xae225c = _0x277a74,
        _0x38d6fb = L[_0xae225c(0x19b)]();
    _0x38d6fb['id'] = 0x2;
    var _0x2e5e78 = this['data'];
    clearDestinations();
    var _0x8937f7 = L[_0xae225c(0x1c2)]({
        'offset': [0x0, -0x14]
    })[_0xae225c(0x1ac)](_0xae225c(0x1aa) + _0x2e5e78[_0xae225c(0x1b6)] + '</strong></p>');
    marker = new L[(_0xae225c(0x189))]([_0x2e5e78[_0xae225c(0x190)], _0x2e5e78[_0xae225c(0x1a6)]], {
        'icon': L[_0xae225c(0x1b0)]({
            'className': _0xae225c(0x18b),
            'html': _0xae225c(0x1c6) + _0x2e5e78[_0xae225c(0x18d)] + _0xae225c(0x1b1),
            'iconAnchor': [0x14, 0x1e]
        })
    })['addTo'](_0x38d6fb), marker[_0xae225c(0x1ab)](_0x8937f7), marker['getPopup']()['on'](_0xae225c(0x1a8),
        markerPopupClose), _0x38d6fb[_0xae225c(0x1cb)](map), marker[_0xae225c(0x1cf)]();
    var _0x4d1ecc = logbook;
    _0x4d1ecc = _0x4d1ecc[_0xae225c(0x1a0)](function(_0x31dda3) {
        var _0x3510b2 = _0xae225c;
        return _0x31dda3[_0x3510b2(0x1cc)][_0x3510b2(0x186)] == _0x2e5e78[_0x3510b2(0x18d)][_0x3510b2(0x1c0)]
        () || _0x31dda3[_0x3510b2(0x1cc)]['arrIcao'] == _0x2e5e78[_0x3510b2(0x18d)][_0x3510b2(0x1c0)]();
    }), dataSet = [];
    for (var _0x36042e in _0x4d1ecc) {
        dataSet[_0xae225c(0x1b2)](_0x4d1ecc[_0x36042e][_0xae225c(0x1cc)]);
        var _0x200938 = _0x4d1ecc[_0x36042e]['airportInfo'][_0xae225c(0x1b3)](_0x7fb8f0 => _0x7fb8f0['icao'] !=
                _0x2e5e78[_0xae225c(0x18d)]),
            _0x2fc636 = {
                'x': _0x2e5e78[_0xae225c(0x1a6)],
                'y': _0x2e5e78[_0xae225c(0x190)]
            };
        if (_0x200938 != null) {
            var _0x502aec = {
                    'x': _0x200938[_0xae225c(0x1a6)],
                    'y': _0x200938['lat']
                },
                _0x3a52ff = new arc[(_0xae225c(0x1b9))](_0x2fc636, _0x502aec),
                _0x2198d4 = _0x3a52ff[_0xae225c(0x1be)](0x64, {
                    'offset': 0xa
                }),
                _0x1ce1e1 = [];
            for (var _0x36042e in _0x2198d4[_0xae225c(0x1b4)][0x0]['coords']) {
                _0x1ce1e1[_0xae225c(0x1b2)]([_0x2198d4['geometries'][0x0][_0xae225c(0x18c)][_0x36042e][0x1], _0x2198d4[
                    _0xae225c(0x1b4)][0x0][_0xae225c(0x18c)][_0x36042e][0x0]]);
            }
            var _0x14750f = L[_0xae225c(0x1a5)](_0x1ce1e1, {
                'color': _0xae225c(0x1a1),
                'weight': 0x2,
                'dashArray': [0x5, 0x5]
            })[_0xae225c(0x1cb)](_0x38d6fb);
            marker = new L[(_0xae225c(0x189))]([_0x200938[_0xae225c(0x190)], _0x200938['lng']], {
                'icon': L[_0xae225c(0x1b0)]({
                    'className': _0xae225c(0x18b),
                    'html': '<div\x20class=\x22label_content\x22><span\x20title=\x22' + _0x200938[
                            _0xae225c(0x1b6)] + '\x22>' + _0x200938[_0xae225c(0x18d)] + _0xae225c(
                        0x1b1),
                    'iconAnchor': [0x14, 0x1e]
                })
            })[_0xae225c(0x1cb)](_0x38d6fb);
        }
        _0x38d6fb[_0xae225c(0x1cb)](map);
    }
    initFlightTable(), _0x38d6fb['addTo'](map), map[_0xae225c(0x195)](_0x38d6fb[_0xae225c(0x185)]());
}

function initFlightTable() {
    var _0x46e7b2 = _0x277a74;
    dataTable != null && dataTable[_0x46e7b2(0x1ae)](), dataTable = $('#flights')[_0x46e7b2(0x1bc)]({
        'initComplete': function() {},
        'data': dataSet,
        'processing': ![],
        'serverSide': ![],
        'language': {
            'sEmptyTable': 'Select\x20a\x20location\x20on\x20the\x20map\x20to\x20list\x20your\x20previous\x20flights.'
        },
        'pageLength': 0x14,
        'columns': [{
            'data': _0x46e7b2(0x1c7)
        }, {
            'data': _0x46e7b2(0x1bd)
        }, {
            'data': _0x46e7b2(0x186)
        }, {
            'data': _0x46e7b2(0x1bb)
        }, {
            'data': 'duration'
        }, {
            'data': _0x46e7b2(0x1cd)
        }, {
            'data': 'approvedStatusDescription'
        }],
        'colReorder': ![],
        'order': [
            [0x0, 'desc']
        ]
    });
}
</script>