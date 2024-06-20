<!doctype html>
<html lang="en-us" class="pf-theme-dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Viewfinder Maturity Assessment</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/brands.css" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/tab.css" />
  <link rel="stylesheet" href="css/patternfly.css" />
  <link rel="stylesheet" href="css/patternfly-addons.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
  <script src="https://kit.fontawesome.com/8a8c57f9cf.js" crossorigin="anonymous"></script>

  <script>
    // Style all the dialog
    $(function() {
      $(".dialog_help").dialog({
        modal: true,
        autoOpen: false,
        width: 500,
        height: 300,
        dialogClass: 'ui-dialog-osx'
      });
    });

    // Opens the appropriate dialog
    $(function() {
      $(".opener").click(function() {
        var id = $(this).data('id');
        $(id).dialog("open");
      });
    });
  </script>
</head>
<body>
  <header class="pf-c-page__header">
    <div class="pf-c-page__header-brand">
      <div class="pf-c-page__header-brand-toggle"></div>
      <a class="pf-c-page__header-brand-link" href="index.php">
        <img class="pf-c-brand" src="images/telescope-viewfinder.png" alt="Viewfinder logo" />
      </a>
    </div>
  </header>
  <div class="container">
    <?php
    $string = file_get_contents("controls.json");
    $json = json_decode($string, true);

    $controls = array_keys($json);

    function getControls($area, $json) {
      $i = 1;
      $qnum = $json[$area]['qnum'];
      $infoId = $qnum . "-" . $i;
      $title = $json[$area]['title'];
      $control = $area;
      print "<p>" . $json[$area]['overview'] . "</p>";
      print "<ul class='ks-cboxtags'>\n";
      while ($i < 8) {
        $summary = $i . '-summary';
        $itemSummary = $json[$area][$summary] ? '&nbsp; <i class="fa-solid fa-circle-info" style="display: inline-block;max-width: 100px;" title="' . $json[$area][$summary] . '"></i>' : "";
        $tier = $i . '-tier';
        $tierClass = "smallText" . $json[$area][$tier];
        $points = $i . "-points";
        print '<li><input type="checkbox" name="' . "control" . $qnum . "-" . $i . "\" id=\"" . "control" . "$qnum" . "-" . $i . '" value="' . $json[$area][$points] . '"><label for="' . "control" . $qnum . "-" . $i . '"><p class="' . $tierClass . '">' . $json[$area][$tier] . '</p>' . $json[$area][$i] . "$itemSummary &nbsp </label></li>\n";
        $i++;
      }
      print "</ul>";
    }
    ?>
    <div class="tab">
      <?php
      $first = 0;
      foreach ($controls as $control) {
        $title = $json[$control]['title'];
        if ($first < 2) {
          print '<button class="tablinks" onclick="openCity(event, \'' . $control . '\')" id="defaultOpen">' . $title . '</button>';
        } else {
          print '<button class="tablinks" onclick="openCity(event, \'' . $control . '\')">' . $title . '</button>';
        }
        $first++;
      }
      ?>
    </div>
  </div>
  <div class="container">
    <form action="results.php">
      <fieldset>
        <?php
        foreach ($controls as $control) {
          print '<div id="' . $control . '" class="tabcontent">';
          getControls($control, $json);
          print '</div>';
        }
        ?>
      </fieldset>
      <br>
      <input class='ui-button ui-widget ui-corner-all' id='submitButton' type='submit' name='Submit' value='Submit'>
    </form>
  </div>
  <script type="text/javascript">
    function openCity(evt, cityName) {
      var i, tabcontent, tablinks;

      tabcontent = document.getElementsByClassName("tabcontent");
      for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
      }

      tablinks = document.getElementsByClassName("tablinks");
      for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
      }

      document.getElementById(cityName).style.display = "block";
      evt.currentTarget.className
