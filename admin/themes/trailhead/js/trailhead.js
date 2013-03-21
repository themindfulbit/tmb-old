// Customize select boxes
$(function(){ 
  if (!$.browser.opera) {
    $('.input-select-wrap select').not('.cell-select select').each(function(){
      var title = $(this).attr('title');
      title = $('option:selected',this).text();
      $(this)
        .css({'z-index':10,'opacity':0,'appearance':'none', '-khtml-appearance':'none', '-webkit-appearance': 'none'})
        .after('<span class="select">' + title + '</span>')
        .change(function(){
          val = $('option:selected',this).text();
          $(this).next().text(val);
        })
    });
  };
});

// Tablesorter
$('.sortable').tablesorter();

// Tooltips
$('.tip').tooltip();

// Datepicker
$('.input-date input').datepicker({format:'yyyy-mm-dd'})
  .on('changeDate', function(ev){
    $(this).datepicker('hide');
});

$('.timepicker').timepicker({
  defaultTime: 'value'
});

// Auto slugger
$('#publish-title').makeSlug({
    slug: $('.auto-slug')
});

// if a default title was set, pre-populate the slug field
$(document).ready(function() {
  if ($("#publish-title").val().length > 0) {
    $("#publish-title").keyup();
  }
});

$(function() {
  $('.input-tags input').tagsInput({
    width:'auto',
    'defaultText':'add an item...'
  });
});


// In CP flash messages
$('#flash-msg')
  .delay(50)
  .animate({'margin-top' : '0'}, 900, 'easeOutBounce')
  .delay(3000)
  .animate({'margin-top' : '-74px'}, 900, 'easeInOutBack');

// Login error message
$('#login #form-errors')
  .delay(50)
  .animate({'margin-top' : '-245px'}, 500, 'easeOutBack');

// Hide iPhone address bar
window.addEventListener("load",function() {
  setTimeout(function(){
    window.scrollTo(0, 1);
  }, 0);
});


// The Almighty Grid!
var checkGridState = function($grid) {
  var max_rows = parseInt($grid.data("maxRows"), 10) || Infinity,
      min_rows = parseInt($grid.data("minRows"), 10) || 0,
      rows = $grid.find("tbody tr").length;

  if (rows >= max_rows) {
    $grid.next("a.grid-add-row").css("opacity", 0.2);
  } else {
    $grid.next("a.grid-add-row").css("opacity", 1);
  }
}

var updateGrid = function($grid) {
  var rows = $grid.find("tbody tr");

  $grid.find("tbody tr").each(function(i) {
    $(this).children("th").text(i + 1);
  });
};

$("a.grid-add-row").live("click", function () {
  var $grid      = $(this).parent().find(".grid:first");
  var row        = $grid.find("tbody:first").children(':last');      
  var row_count  = $grid.find("tbody tr").length;
  var replaceKey = false;
  var max_rows   = $grid.data("maxRows") || Infinity;

  if (row_count >= max_rows) {
    return false;
  }
  $grid.append($grid.data("emptyRow").replace(/page\[yaml\]\[([\w\d_\-]+)\]\[(\d+)\]\[([\w\d_\-]+)\]/ig, 'page[yaml][$1][' + (row_count) + '][$3]')).find("tbody tr:last-child > th:first-child").html(row_count + 1);
  $grid.trigger('addRow');

  checkGridState($grid);
  updateGrid($grid);
  return !1;
});

$("a.grid-delete-row").live("click", function() {
  var message,
      $grid = $(this).closest(".grid"),
      min_rows = $grid.data("minRows") || 0;

  // prevent row deletion if min_rows is set and this would go under that
  if ($grid.find("tbody tr").length <= min_rows) {
    if (min_rows > 0) {
      message = "This grid requires at least " + min_rows + " row";
      message += (min_rows === 1) ? "." : "s.";
      alert(message);
    }

    $(this).addClass("confirm").html('<span class="icon">u</span>');
    checkGridState($grid);
    updateGrid($grid);
    return false;
  }

  $(this).closest('tr').remove();
  checkGridState($grid);
  updateGrid($grid);
  return false;
});

var stick_helper_width = function(e, tr) {
  var $originals = tr.children();
  var $helper = tr.clone();
  $helper.children().each(function(index) {
    $(this).width($originals.eq(index).width())
  });
  return $helper;
};

$(".grid tbody").sortable({
  helper: stick_helper_width,
  handle: 'th.drag-indicator',
  placeholder: 'drag-placeholder',
  forcePlaceholderSize: true,
  axis: 'y',

  start: function (event, ui) {
    var num_cols = $(this).find('tr')[0].cells.length;
    ui.placeholder.html('<td colspan='+num_cols+'>&nbsp;</td>');
  },

  update: function(event, ui) {
    $(event.target).find('> tr').each(function() {

      row_number = $(this).index();

      $(this).children("th:first-child").html(row_number+1)
      $(this).find("input, textarea, select").each(function() {

        var replaceCount = 0;
        var replaceKey = false;

        if (false == replaceKey) {
          replaceKey = this.name.match(/\[(\d+)\]/g).length;
        }

        this.name = this.name.replace(/\[(\d+)\]/g, function (e, a) {
          replaceCount++;

          if (replaceCount == replaceKey) {
            return "[" + row_number + "]";
          } 

          return "["+a+"]";
        });

      });

    })
  }
});

$('.confirm').click(function() {
  $(this).removeClass('confirm');
  $(this).text("Do it.");
  $(this).unbind();
  return false;
});