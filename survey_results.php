<?php
$conn = new mysqli("localhost", "root", "", "survey_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM surveys");
$total = $result->num_rows;

if ($total > 0) {
    $ages = [];
    $pizza = $pasta = $pap = 0;
    $movies = $radio = $eatout = $tv = 0;

    while ($row = $result->fetch_assoc()) {
        $age = date_diff(date_create($row['dob']), date_create('today'))->y;
        $ages[] = $age;

        $foods = explode(',', $row['favorite_food']);
        if (in_array('Pizza', $foods)) $pizza++;
        if (in_array('Pasta', $foods)) $pasta++;
        if (in_array('Pap and Wors', $foods)) $pap++;

        $movies += $row['movies_rating'];
        $radio += $row['radio_rating'];
        $eatout += $row['eatout_rating'];
        $tv += $row['tv_rating'];
    }

    $avg_age = array_sum($ages) / count($ages);
    $max_age = max($ages);
    $min_age = min($ages);

    $pizza_pct = round($pizza / $total * 100, 1);
    $pasta_pct = round($pasta / $total * 100, 1);
    $pap_pct = round($pap / $total * 100, 1);

    $avg_movies = round($movies / $total, 1);
    $avg_radio = round($radio / $total, 1);
    $avg_eatout = round($eatout / $total, 1);
    $avg_tv = round($tv / $total, 1);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Survey Results</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 800px; margin: 40px auto; border: 2px solid #000; padding: 40px; }
    </style>
</head>
<body>
<div class="container">
    <div style="text-align:right;">
        <a href="survey_form.php">FILL OUT SURVEY</a> |
        <a href="survey_results.php">VIEW SURVEY RESULTS</a>
    </div>
    <h2>_ Surveys</h2>
    <h2 style="text-align:center;">Survey Results</h2>
    <?php if ($total == 0): ?>
        <p>No Surveys Available</p>
    <?php else: ?>
        <table style="margin:auto;">
            <tr><td>Total number of surveys :</td><td><?=$total?></td></tr>
            <tr><td>Average Age :</td><td><?=round($avg_age, 1)?></td></tr>
            <tr><td>Oldest person who participated in survey :</td><td><?=$max_age?></td></tr>
            <tr><td>Youngest person who participated in survey :</td><td><?=$min_age?></td></tr>
            <tr><td>Percentage of people who like Pizza :</td><td><?=$pizza_pct?> %</td></tr>
            <tr><td>Percentage of people who like Pasta :</td><td><?=$pasta_pct?> %</td></tr>
            <tr><td>Percentage of people who like Pap and Wors :</td><td><?=$pap_pct?> %</td></tr>
            <tr><td>People who like to watch movies :</td><td><?=$avg_movies?></td></tr>
            <tr><td>People who like to listen to radio :</td><td><?=$avg_radio?></td></tr>
            <tr><td>People who like to eat out :</td><td><?=$avg_eatout?></td></tr>
            <tr><td>People who like to watch TV :</td><td><?=$avg_tv?></td></tr>
        </table>
    <?php endif; ?>
</div>
</body>
</html>