<!doctype html>
<html lang="en-us" class="pf-theme-dark">
<head>
  <title>Viewfinder - Results</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/table.css">
  <link rel="stylesheet" href="css/patternfly.css" />
  <link rel="stylesheet" href="css/patternfly-addons.css" />
  <link rel="stylesheet" href="css/tab.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.6/d3.min.js" charset="utf-8"></script>
  <script src="https://kit.fontawesome.com/8a8c57f9cf.js" crossorigin="anonymous"></script>
  <script>
    $(function() {
      $("#accordion").accordion({
        heightStyle: "content",
        collapsible: true,
        active: 'none'
      });
    });
  </script>
</head>
<body>
  <header class="pf-c-page__header">
    <div class="pf-c-page__header-brand">
      <a class="pf-c-page__header-brand-link" href="index.php">
        <img class="pf-c-brand" src="images/telescope-viewfinder.png" alt="Viewfinder logo" />
      </a>
    </div>
  </header>
  <?php

  $urlData = "./report/index.php?" . $_SERVER["QUERY_STRING"];
  parse_str($_SERVER["QUERY_STRING"], $data);
  $string = file_get_contents("controls.json");
  $json = json_decode($string, true);

  $controls = array_keys($json);
  
  $controlTotal = array_fill(0, 9, 0);
  $controlDetails = array(array_fill(0, 9, 0));

  foreach ($data as $field => $value) {
    if (strpos($field, "control") !== false) {
      $controlNumber = substr($field, 7, 1);
      $controlTotal[$controlNumber] += $value;
    }
  }

  function getRating($score) {
    $rating = "Foundation";
    if ($score > 9 && $score < 22) {
      $rating = "Strategic";
    } elseif ($score > 27) {
      $rating = "Advanced";
    }
    return $rating;
  }

  function getTotalRating($score) {
    $rating = "Foundation";
    if ($score > 84 && $score < 168) {
      $rating = "Strategic";
    } elseif ($score > 169) {
      $rating = "Advanced";
    }
    return $rating;
  }

  $totalScore = 0;

  ?>

  <div class="container">

    <div class="tab">
      <button class="tablinks" onclick="openTab(event, 'Radar')" id="defaultOpen">Radar Chart & Maturity Levels</button>
      <button class="tablinks" onclick="openTab(event, 'DetailedOutput')">Recommendations</button>
      <button class="tablinks" onclick="openTab(event, 'NextSteps')">Agenda</button>
      <button class="tablinks"><a href="<?php print $urlData; ?>" target= _blank>Detailed Output</a></button>
    </div>

    <div id="Radar" class="tabcontent">

      <div class="htmlChart">
        <div class="radarChart"></div>
      </div>
      <div class="bigtableLeft">

        <table class="spacedTable">
          <thead>
            <tr>
              <th>Control</th>
              <th>Rating</th>
            </tr>
          </thead>
          <?php
          $totalScore = 0;
          foreach ($controls as $control) {
            print "<tr>";
            $title = $json[$control]['title'];
            $qnum = $json[$control]['qnum'];
            $score = $controlTotal[$qnum];
            $totalScore += $score;
            print "<td>" . $title . "</td>";
            print "<td class='cell" . getRating($score) . "'>" . getRating($score) . " ($score out of 36)</td>";
            print "</tr>";
          }
          print '</table>';
          print "<br><table><td class='cell" . getTotalRating($totalScore) . "'>Overall rating: " . getTotalRating($totalScore) . " ($totalScore out of 252)</td></tr></table>";
          ?>
      </div>
    </div>
    <!-- Detailed Output -->
    <div id="DetailedOutput" class="tabcontent">

      <div id="accordion">
        <?php
        foreach ($controls as $control) {
          $highest = 0;
          $qnum = $json[$control]['qnum'];
          $score = $controlTotal[$qnum];
          $title = $json[$control]['title'];
          print "<h3>$title <span class='cellHeader" . getRating($score) . "'>" . getRating($score) . "</span></h3><div>";

          $levelArray = array();
          foreach ($data as $key => $value) {
            if (preg_match("/^control$qnum-[0-9]*/", $key)) {
              array_push($levelArray, substr($key, -1));
              $highest++;
            }
          }
          $nextLevel = $highest + 1;
          if ($nextLevel < 9) {
            $nextRecommendation = $nextLevel . '-recommendation';
            $nextSummary = $nextLevel . '-summary';
            print "<h4 class=title-text>Recommendation</h4>";
            print "<p>Start to work on preparing for actions concerning " . $json[$control][$nextLevel] . " (Level $nextLevel)<p>";
            print "<br><p class=why-what>What is " . $json[$control][$nextLevel] . " ?</p><p>" . $json[$control][$nextSummary] . "</p>";

            if ($json[$control][$nextRecommendation] != "") {
              print "<br><p class=why-what>How</p>";
              print "<p>" . $json[$control][$nextRecommendation] . "<p>";
              array_push($nextSteps, $json[$control][$nextLevel]);
              array_push($nextStepsHow, $json[$control][$nextSummary]);
            }
            print "<br><h4 class=why-what>Conversations to have</h4>";
            $nextConversations = $nextLevel . '-conversation';
            if ($json[$control][$nextConversations] != "") {
              print $json[$control][$nextConversations];
            } else {
              print "No conversations currently for " . $json[$control][$nextLevel];
            }
          } else {
            print "<p>You're doing great as you are!</p>";
          }

          $allLevels = range(1, max($levelArray));
          $missing = array_diff($allLevels, $levelArray);
          if ($missing) {
            print "<br><br><h4 class=why-what>Skipped Level(s)</h4>";
            foreach ($missing as $notthere) {
              $skippedRecommendation = $notthere . '-recommendation';
              print "Level $notthere - ";
              if ($json[$control][$skippedRecommendation] != "") {
                print $json[$control][$skippedRecommendation] . ". ";
              } else {
                $notthereComment = $notthere . "-summary";
                print $json[$control][$notthereComment];
              }
              print "<br>";
            }
          }
          print "</div>";
        }
        ?>
      </div>
      <!-- End of Detailed Output -->
    </div>
    <div id="NextSteps" class="tabcontent">
      <div class="nextStepsPage">
        <table class="paleBlueRows">
          <thead>
            <tr>
              <th>Time</th>
              <th>Agenda</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>09:00-09:15</td>
              <td>Welcome &amp; Introductions</td>
            </tr>
            <tr>
              <td>09:15-10:00</td>
              <td><?php echo $nextSteps[0]; ?></td>
            </tr>
            <tr>
              <td>10:00-10:45</td>
              <td><?php echo $nextSteps[1]; ?></td>
            </tr>
            <tr>
              <td class="agendaBreak">10:45-11:00</td>
              <td class="agendaBreak">Break</td>
            </tr>
            <tr>
              <td>11:00-11:45</td>
              <td><?php echo $nextSteps[2]; ?></td>
            </tr>
            <tr>
              <td>11:45-12:30</td>
              <td><?php echo $nextSteps[3]; ?></td>
            </tr>
            <tr>
              <td class="agendaBreak">12:30-13:00</td>
              <td class="agendaBreak">Lunch</td>
            </tr>
            <tr>
              <td>13:00-13:45</td>
              <td><?php echo $nextSteps[4]; ?></td>
            </tr>
            <tr>
              <td>13:45-14:30</td>
              <td><?php echo $nextSteps[5]; ?></td>
            </tr>
            <tr>
              <td class="agendaBreak">14:30-14:45</td>
              <td class="agendaBreak">Break</td>
            </tr>
            <tr>
              <td>14:45-15:30</td>
              <td><?php echo $nextSteps[6]; ?></td>
            </tr>
            <tr>
              <td>15:30-16:00</td>
              <td>Wrap Up &amp; Next Steps</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="js/radarChart.js"></script>
  <script>
    var margin = {
        top: 100,
        right: 100,
        bottom: 100,
        left: 100
      },
      width = Math.min(700, window.innerWidth - 10) - margin.left - margin.right,
      height = Math.min(width, window.innerHeight - margin.top - margin.bottom - 20);

    var data = [
      [
        {axis: "AC", value: <?php echo $controlTotal[1]; ?>},
        {axis: "AT", value: <?php echo $controlTotal[2]; ?>},
        {axis: "AU", value: <?php echo $controlTotal[3]; ?>}
      ]
    ];

    var color = d3.scale.ordinal().range(["#CC333F", "#00A0B0"]);

    var radarChartOptions = {
      w: width,
      h: height,
      margin: margin,
      maxValue: 0.5,
      roundStrokes: true,
      color: color
    };

    RadarChart(".radarChart", data, radarChartOptions);
  </script>
  <script type="text/javascript">
    function openTab(evt, cityName) {
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
      evt.currentTarget.className += " active";
    }
  </script>
  <script type="text/javascript">
    document.getElementById("defaultOpen").click();
  </script>
</body>
</html>
