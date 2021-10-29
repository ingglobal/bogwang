<?php
$sub_menu = "960100";
include_once('./_common.php');

auth_check($auth[$sub_menu],"r");

$g5['title'] = '가동데이터 그래프';
include_once('./_top_menu_data.php');
include_once('./_head.php');
echo $g5['container_sub_title'];
?>
<style>
.xbuttons {float:right;margin-right:8px;margin-bottom:3px;}
.xbuttons a {margin-left:2px;border:solid 1px #ddd;padding:2px 6px;}
#report {display:none;position:absolute;top:0;left:0;}
#report span {border:solid 1px #bbb;}
#fchart {margin:0 0;}
/* #fchart .dta_value_type {display:inline-block;} */
#fchart .chr_name {font-weight:bold;font-size:1.1em;margin-right:6px;}
</style>

<!-- <script src="<?php echo G5_URL?>/lib/highcharts/Highcharts/code/highcharts.src.js"></script> -->
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/highstock.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/modules/data.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/Highstock/code/modules/exporting.js"></script>
<script src="<?php echo G5_URL?>/lib/highcharts/moment.js"></script><!-- 다양한 시간 표현을 위한 플러그인 -->

<div class="div_dta_type">
    <a href="javascript:" id="1_1_0" mms_idx="1" dta_type="1" dta_no="0">온도0</a>
    <a href="javascript:" id="1_1_1" mms_idx="1" dta_type="1" dta_no="1">온도1</a>
    <a href="javascript:" id="1_2_0" mms_idx="1" dta_type="2" dta_no="0">토크0</a>
    <a href="javascript:" id="1_3_0" mms_idx="1" dta_type="3" dta_no="0">전류0</a>
    <a href="javascript:" id="1_7_0" mms_idx="1" dta_type="7" dta_no="0">습도0</a>
</div>

<div class="local_desc01 local_desc" style="display:no ne;">
    <p>그래프 좌표갯수 Max값은 <span class="color_red"><?=$g5['setting']['set_graph_max']?>개</span>입니다. (로딩속도 최적화를 필요한 제약입니다.) 시간 범위를 크게 잡더라도 좌표에 표현하는 갯수는 자동으로 <span class="color_red"><?=$g5['setting']['set_graph_max']?>개까지만</span> 표현됩니다. (시작시점 기준)</p>
</div>

<div class="graph_wrap" style="">
    <span id="report">
        <span id="xmin">xmin</span>
        <span id="xmax">xmax</span>
        <span id="ymin">ymin</span>
        <span id="ymax">ymax</span>
    </span>
    <div class="xbuttons">
        <a href="javascript:" class="btn_orig" title="초기화"><i class="fa fa-bars"></i></a>
        <a href="javascript:" class="btn_smaller" title="작게"><i class="fa fa-compress"></i></a>
        <a href="javascript:" class="btn_bigger" title="크게"><i class="fa fa-expand"></i></a>
    </div>

    <!-- 차트 -->
    <div id="chart1" style="position:relative;width:100%; height:400px;">
    </div>

    <!-- 컨트롤 -->
    <div class="chr_select" style="text-align:center;">
        <div id="fchart" name="fchart" class="local_sch01 local_sch" method="get">
        <input type="hidden" name="chr_id" style="width:70px">
        <input type="hidden" name="chr_idx" style="width:20px">
            <span class="chr_name">항목명</span>
            <span class="chr_item_name">증폭:</span>
            <input type="text" name="chr_amp" value="2" id="chr_amp" class="frm_input" style="width:40px;" placeholder="증폭">배, 
            <span class="chr_item_name">값변동</span>
            <input type="text" name="chr_move" value="200" id="chr_move" class="frm_input" style="width:45px;" placeholder="값변동">
            <div class="dta_value_type" style="display:none;">
                <label class="dv_radio"><input type="radio" name="dv_type" value="" id="dv_real" checked>측정값</label>
                <label class="dv_radio"><input type="radio" name="dv_type" value="" id="dv_sum">합계</label>
                <label class="dv_radio"><input type="radio" name="dv_type" value="" id="dv_avg">평균</label>
                <label class="dv_radio"><input type="radio" name="dv_type" value="" id="dv_max">최대</label>
                <label class="dv_radio"><input type="radio" name="dv_type" value="" id="dv_min">최저</label>
            </div>
            <script>
                $('input[name=dv_type]:checked').closest('label').addClass('active');
                $(document).on('click','input[name=dv_type]',function(e){
                    $('input[name=dv_type]').closest('label').removeClass('active');
                    $('input[name=dv_type]:checked').closest('label').addClass('active');
                });
            </script>
            <button type="submit" class="btn btn_01"><i class="fa fa-check"></i> 적용</button>
            <a href="javascript:" class="btn btn_02 btn_chr_del" title="적용">제거 <i class="fa fa-times"></i></a>
        </div>
        <script>
        // 변형 그래프 적용 =========================================================================
        $(document).on('click','button[type=submit]',function(e){
            var chr_idx_chg = $('#fchart').find('input[name=chr_idx]').val();
            var chr_amp = $('#fchart').find('input[name=chr_amp]').val();
            var chr_move = parseInt($('#fchart').find('input[name=chr_move]').val());
            console.log(chr_idx_chg+'/'+chr_amp+'/'+chr_move);
            console.log(typeof(chr_idx_chg)+'/'+typeof(chr_amp)+'/'+typeof(chr_move));
            // console.log(seriesOptions[chr_idx_chg].data);
            for(i=0;i<seriesOptions[chr_idx_chg].data.length;i++) {
                // console.log(seriesOptions[chr_idx_chg].data[i]);
                old_y = seriesOptions[chr_idx_chg].data[i].y;
                seriesOptions[chr_idx_chg].data[i].y = (old_y*chr_amp)+chr_move;
            }
            // // seriesOptions[chr_idx_chg] = {
            // //     name: dta_name,
            // //     id:chr_id,
            // //     data: data
            // // };
            createChart();
        });
        </script>
    </div>

<script>
var seriesOptions = [],
    chart, options;

function createChart() {
    // var chart = new Highcharts.stockChart({
    options = {
        chart: {
            renderTo: 'chart1',
            type: 'spline',   // line, spline, area, areaspline, column, bar, pie, scatter, gauge, arearange, areasplinerange, columnrange
            events: {
                redraw: function() {
                    $('#xmin').text(this.xAxis[0].min);
                    $('#xmax').text(this.xAxis[0].max);
                    $('#ymin').text(this.yAxis[0].min);
                    $('#ymax').text(this.yAxis[0].max);
                    // console.log(this.yAxis[0].max);
                    // console.log(this.yAxis[0].min);
                },
                load: function() {
                    $('#xmin').text(this.xAxis[0].min);
                    $('#xmax').text(this.xAxis[0].max);
                    $('#ymin').text(this.yAxis[0].min);
                    $('#ymax').text(this.yAxis[0].max);
                    // console.log(this.yAxis[0].max);
                    // console.log(this.yAxis[0].min);
                }
            },
        },
        
        animation: false,

        xAxis: {
            // min: 1587635789000,
            // max: 1587643939000,
            type: 'datetime',
            labels: {
                formatter: function() {
                    return moment(this.value).format("MM/DD HH:mm");
                }
            },
            events: {
                setExtremes: function (e) {
                    $('#xmin').text(e.min);
                    $('#xmax').text(e.max);
                }
            }
        },

        yAxis: {
            // max: 1800,   // 크게 확대해서 보려면 20
            // min: -100,  // 크게 확대해서 보려면 -10, 없애버리면 자동 스케일
            showLastLabel: true,    // 위 아래 마지막 label 보임 (이게 없으면 끝label이 안 보임)
            scrollbar: {
                enabled: true
            },
            opposite: false,
            tickInterval: null,
            // minorTickInterval: 5,
            // minorTickLength: 0,
        },

        plotOptions: {
            series: {
                showInNavigator: true,
                events: {
                    legendItemClick: function (e) {
                        e.preventDefault();
                        // console.log(this);
                        // console.log(this.userOptions);
                        var chr_id = this.userOptions.id;
                        var chr_idx = this.userOptions._symbolIndex;
                        var chr_name = this.userOptions.name;
                        $('#fchart input[name=chr_id]').val(chr_id);
                        $('#fchart input[name=chr_idx]').val(chr_idx);
                        $('#fchart .chr_name').text(chr_name);
                    }
                },
                dataGrouping: {
                    enabled: false, // dataGrouping 안 함 (range가 변경되면 평균으로 바뀌어서 헷갈림)
                }
                // states: {
                //     inactive: {
                //         opacity: 0
                //     }
                // },
            }
        },

        navigator: {
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: {
                    second: '%H:%M:%S',
                    minute: '%H:%M',
                    hour: '%H:%M',
                    day: '%m-%d',
                    week: '%m-%d',
                    month: '%Y-%m',
                    year: '%Y-%m'
                }
            },
        },

        navigation: {
            buttonOptions: {
                enabled: false, // contextButton (인쇄, 다운로드..) 설정 (기본옵션 사용자들에게는 안 보이게!!)
                align: 'right',
                x: -20,
                y: 15
            }
        },

        legend: {
            enabled: true,
        },

        rangeSelector: {
            enabled: false,
        },

        tooltip: {
            formatter: function(e) {
                // var tooltip1 =  moment(this.x).format("YYYY-MM-DD HH:mm:ss");
                var tooltip1 =  moment(this.x).format("MM/DD HH:mm:ss");
                // console.log(this);
                $.each(this.points, function () {
                    // console.log(this);
                    tooltip1 += '<br/><span style="color:' + this.color + '">\u25CF '+this.series.name+'</span>: <b>' + this.y + '</b>';
                    if(this.point.y!=this.point.yrow) {
                        tooltip1 += '<span style="color:darkorange;"> (raw:' + this.point.yrow + ')</span>';
                    }
                    tooltip1 += '<br/>';
                });
                return tooltip1;
            },
            split: false,
            shared: true
        },
        series: seriesOptions
        
    };

    chart = new Highcharts.stockChart(options);
    dta_loading('hide');
    
}

// 그래프 호출 =========================================================================
$(document).on('click','.div_dta_type a',function(e){
    e.preventDefault();
    // console.log ( $(this).attr('id') );
    var chr_exists = null;
    for(i=0;i<seriesOptions.length;i++) {
        // console.log(seriesOptions[i]);
        // console.log(seriesOptions[i].id);
        if(seriesOptions[i].id==$(this).attr('id'))
            chr_exists = 1;
    }
    if(chr_exists) {
        alert('이미 적용된 그래프입니다.');
        return false;
    }
    dta_loading('show');
    chr_idx = chart ? (chart.series.length/2) : 0;  // navigator series가 있으므로 2배 숫자가 나옴 (navigation series가 있어서 따로 +1을 하지 않아도 됨)
    dta_name = $(this).text();
    mms_idx = $(this).attr('mms_idx');
    dta_type = $(this).attr('dta_type');
    dta_no = $(this).attr('dta_no');
    chr_id = mms_idx+'_'+dta_type+'_'+dta_no;
    $(this).attr('chr_id',chr_id);

    Highcharts.getJSON(
        '//icmms.co.kr/device/json/measure.php?token=1099de5drf09&mms_idx='+mms_idx+'&dta_type='+dta_type+'&dta_no='+dta_no+'&start_date=2020-04-23&start_time=19:00:00&end_date=2020-04-23&end_time=23:59:59',
        function(data) {
            seriesOptions[chr_idx] = {
                name: dta_name,
                id:chr_id,
                data: data
            };
            createChart();
        }
    );
});

// 이게 위치가 클릭 선언 뒤쪽으로 와야 하는구만!
$('.div_dta_type a').eq(0).trigger('click');


$('.btn_bigger, .btn_orig, .btn_smaller').click(function(e) {
    var act = $(this).attr('class');    // btn_bigger, btn_orig, btn_smaller
    // $("#chart1").empty();
    y1 = parseInt($('#ymin').text());
    y2 = parseInt($('#ymax').text());
    ydiff = parseInt($('#ymax').text()) - parseInt($('#ymin').text());
    yhalf = ydiff/2;    // 작게 할 때는 1/2 단위 기준으로 양쪽 한 단위값 추가해서 작게 보이게..
    yquar = ydiff/4;    // 크게 할 때 1/4 단위 기준으로 양쪽 한 단위값 제거해서 크게 보이게
    xmin = parseInt($('#xmin').text());   // 크게 작게 하더라도 x좌표 현재값은 유지되어야 함
    xmax = parseInt($('#xmax').text());   // 크게 작게 하더라도 x좌표 현재값은 유지되어야 함
    if(act=='btn_bigger') {
        ymin = y1 + yquar;
        ymax = y2 - yquar;
        ytick = parseInt((ymax-ymin)/8);     // tickInterval
    }
    else if(act=='btn_smaller') {
        ymin = y1 - yhalf;
        ymax = y2 + yhalf;
        ytick = parseInt((ymax-ymin)/8);     // tickInterval
    }
    else {
        // xmin = null,   // 초기화 x좌표 초기화
        // xmax = null,   // 초기화 x좌표 초기화
        ymin = null;
        ymax = null;
        ytick = null;     // tickInterval
    }

    options.xAxis = {
        min: xmin,
        max: xmax,
        labels: {
            formatter: function() {
                return moment(this.value).format("MM/DD HH:mm");
            }
        },
        events: {
            setExtremes: function (e) {
                $('#xmin').text(e.min);
                $('#xmax').text(e.max);
            }
        }
    };
    options.yAxis = {
        min: ymin,
        max: ymax,
        showLastLabel: true,    // 위 아래 마지막 label 보임 (이게 없으면 끝label이 안 보임)
        scrollbar: {
            enabled: true
        },
        opposite: false,
        tickInterval: ytick,    // 눈금 크기(로그, 대수 형태로 계산한다는 데.. 모르겠다.)
    };
    chart = new Highcharts.stockChart(options);

});

// 차트 제거하기
$(document).on('click','.btn_chr_del',function(e){
    e.preventDefault();
    // 차트가 한개뿐이면 제거 안 함
    if($('.div_dta_type a[chr_id]').length==1) {
        alert('차트가 한개뿐이잖아요. 제거 안 할래요!');
        return false;
    }
    var frm = $('#fchart');
    var chr_id_del = frm.find('input[name=chr_id]').val();
    var chr_idx_del = frm.find('input[name=chr_idx]').val();
    if(chr_id_del=='') {
        alert('제거할 차트를 선택하세요.');
        return false;
    }
    else {
        seriesOptions.splice(chr_idx_del,1);
        $('.div_dta_type a[id='+chr_id_del+']').removeAttr('chr_id');
        frm.find('input[name=chr_id]').val('');    // 폼초기화
        createChart();
    }

});

// 로딩 spinner 이미지 표시/비표시
function dta_loading(flag) {
    var img_loading = $('<i class="fa fa-spin fa-spinner" id="spinner" style="position:absolute;top:80px;left:46%;font-size:4em;"></i>');
    if(flag=='show') {
        // console.log('show');
        $('#chart1').append(img_loading);
    }
    else if(flag=='hide') {
        // console.log('hide');
        $('#spinner').remove();
    }
}
</script>


</div>

<script>
$(function(e) {

});
</script>

<?php
include_once ('./_tail.php');
?>
