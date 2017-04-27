<?php
session_start();
if ($_SESSION['valid'] != true) {
    die("failure: user not logged in.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Edit Membership</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">

    <!-- Bootstrap core CSS -->
    <link href="bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="../../assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="dashboard.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]>
    <script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="../../assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>

        .input1[type=text], .input2[type=password] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .button10 {
            background-color: #E35E1B;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .cancelbutton {
            width: auto;
            padding: 10px 18px;
            background-color: #f44336;
            color: white;
            margin: 8px 0;
            border: none;
            cursor: pointer;
        }

        .container {
            padding: 16px;
        }


    </style>
</head>
<body>
<h2>
    <?php
    if ($_SESSION['valid'] == true) {
        include 'logoutNav.php';
    } else {
        include 'loginNav.php';
    }
    ?>
</h2>

<?php
//connect to database
$link = mysql_connect('webhost.engr.illinois.edu', 'eventcoord_cs411', 'cs411');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db('eventcoord_new411');

//get GET variables
$user_id = $_SESSION['id'];
$team_name = htmlspecialchars($_GET['team_name']);
$event_name = htmlspecialchars($_GET['event_name']);
$event_start = htmlspecialchars($_GET['event_start']);
$event_end = htmlspecialchars($_GET['event_end']);
//query for our team and populate all releant variables
$sql="SELECT * FROM Team WHERE name = '$team_name' AND event_name = '$event_name' AND event_start = '$event_start' AND event_end = '$event_end'";
$res=mysql_query($sql);
if (mysql_num_rows($res) == 0) {
    die("No such team ($team_name, $event_name, $event_start, $event_end) exists.");
}
$data=mysql_fetch_assoc($res);
$team_leader_id = $data['leader_id'];
$team_desc = $data['description'];

$sql="SELECT * FROM Event WHERE name = '$event_name' AND start = '$event_start' AND end = '$event_end'";
$res=mysql_query($sql);
if (mysql_num_rows($res) == 0) {
    die("No such event ($event_name, $event_start, $event_end) exists.");
}
$data=mysql_fetch_assoc($res);
$event_desc = $data['description'];
$event_organizer_id = $data['organizer_id'];
$event_max_skill = $data['max_skill'];

//validate membership
$sql="SELECT * FROM Member WHERE user_id = '$user_id' AND team_name = '$team_name' AND event_name = '$event_name' AND event_start = '$event_start' AND event_end = '$event_end'";
$res=mysql_query($sql);
if (mysql_num_rows($res) == 0) {
    print("<p>No such membership ($user_id, $team_name, $event_name, $event_start, $event_end) exists.</p>");
    print("<button class=\"cancelbutton\" onclick=\"window.location.href='team_members.php?team_name=$team_name&event_name=$event_name&event_start=$event_start&event_end=$event_end'\">Back</button>");
    die();

}
$data=mysql_fetch_assoc($res);
$user_skill = $data['skill_name'];
$user_desc = $data['skill_desc'];

 $sql="SELECT * FROM Event WHERE name = '$event_name' AND start = '$event_start' AND end = '$event_end'";
    $res=mysql_query($sql);
    if (mysql_num_rows($res) > 0) {
        $data = mysql_fetch_assoc($res);
        $event_desc = $data['description'];
        $event_organizer_id = $data['organizer_id'];
        $event_max_skill = $data['max_skill'];

        $status_str = ["Upcoming", "Active", "Expired"];
        $status = 0;
        $event_start_formatted = new DateTime($event_start);
        $event_end_formatted = new DateTime($event_end);
        $now_datetime = new DateTime();
        if ($now_datetime >= $event_start_formatted) {
            $status = 1;
        }
        if ($now_datetime > $event_end_formatted) {
            $status = 2;
        }
    }

if ($status > 1) {
    print("<p>You cannot edit your role in this team.</p>");
    print("<button class=\"cancelbutton\" onclick=\"window.location.href='team_members.php?team_name=$team_name&event_name=$event_name&event_start=$event_start&event_end=$event_end'\">Back</button>");
    die();
}
?>

<div class="container">
    <?php
    print("<h3 class=\"sub-header\">Editing your role in in: <a href='team_page.php?team_name=$team_name&event_name=$event_name&event_start=$event_start&event_end=$event_end'>$team_name</a></h3>");
    print("<h3 class=\"sub-header\">Participating in: <a href='event_page.php?event_name=$event_name&event_start=$event_start&event_end=$event_end'>$event_name</a></h3>");
    print("<form action=\"team_member_edit_submit.php?team_name=$team_name&event_name=$event_name&event_start=$event_start&event_end=$event_end\" method=\"post\" target=\"_top\">");
    ?>
        <label style=color:black;font-family:verdana><b>Role name (categorical so we can update your statistics)</b></label>
        <br/>
        <select name="roleName">
            <?php
            //query available roles
            $sql = "SELECT DISTINCT skill_name AS name FROM Has_Skill WHERE user_id = '$user_id' AND score <= $event_max_skill";
            $res = mysql_query($sql);

            if (mysql_num_rows($res) == 0) {
                die("Catastrophic failure: Failed to get skill list");
            }

            //populate drop down list
            while ($data = mysql_fetch_assoc($res)) {
                if ($data['name'] == $user_skill) {
                    print("<option value = \"{$data['name']}\" selected>{$data['name']}</option>");
                }
                else {
                    print("<option value = \"{$data['name']}\">{$data['name']}</option>");
                }
            }
            ?>
        </select>
        <br/>

        <label style=color:black;font-family:verdana><b>Role description (freeform so you can make yourself seem more special than anyone else with the same skill category)</b></label>
        <?php
        print("<input type=\"text\" class=\"input1\" value=\"$user_desc\" name=\"roleDesc\" required>");
        ?>

        <button class="button10" type="submit">Submit</button>
    </form>
    <?php
    print("<button class='button10' onclick=\"window.location.href='team_leave.php?team_name=$team_name&event_name=$event_name&event_start=$event_start&event_end=$event_end'\">Leave Team</button>");
    ?>
</div>

<div class="container" style="background-color:#EEEEEE">
    <?php
    print("<button class=\"cancelbutton\" onclick=\"window.location.href='team_members.php?team_name=$team_name&event_name=$event_name&event_start=$event_start&event_end=$event_end'\">Back</button>");
    ?>
</div>


</body>
</html>
