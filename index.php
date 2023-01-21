<?php
include_once 'db.php';

if (isset($_POST['submit'])) {
  $name_task = $_POST['name_task'];
  $datetime_end = $_POST['datetime_end'];
  $info = $_POST['info'];

  $sql = "INSERT INTO `tasks` (`id`, `name`, `status`, `time_add`, `time_done`,`time_end`, `info`) VALUES
            (NULL, '$name_task', 'work', current_timestamp(), NULL, '$datetime_end', '$info');";
  mysqli_query($db, $sql);
}


$stm = "SELECT * FROM tasks";
$que = mysqli_query($db, $stm);
$rows = mysqli_fetch_all($que);

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/format_css_code.css">
  <link rel="stylesheet" href="css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
  <title>ToDo</title>
</head>

<body>
  <div class="add_task">
    <div class="container">
      <h2>Add Task</h2>
      <form action="" method="POST">
        <input type="text" name="name_task" placeholder="Write Title Task...">
        <input type="date" name="datetime_end">
        <textarea name="info" placeholder="Write More Informtion About The Task..."></textarea>
        <input type="submit" name="submit" value="send">
      </form>
    </div>
  </div>
  <div class="show_task">
    <div class="container">
      <div class="work box">
        <h3 class="title_section">Work Tasks</h3>
        <div class="tasks">
          <?php
          // Function For Format Time For => [addTask, endTask].
          function time_fun($rowDate)
          {
            return date_format(date_create($rowDate), "Y-m-d");
          }

          // If No Task Here Print It.
          $count_task_work = 0;
          $count_task_done = 0;
          $no_task = '<p class="no_task">No Thing Here.</p>';

          foreach ($rows as $row) {
            if ($row[2] == 'work') {
              ++$count_task_work;
              echo "<div class='box'>";
              print "
                  <div class='info'>
                    <span class='title'>$count_task_work- $row[1]</span>
                    <div class='actions'>
                      <i class='fa-solid fa-pen-to-square icon_edit$row[0] icon'></i>
                      <span class='date'>
                        <span>Time Add: " . time_fun($row[3]) . "</span>
                        <span>Time End: " . time_fun($row[4]) . "</span>
                      </span>
                      <form action='' method='POST'>
                        <input type='submit' value='Done' name='send_edit' class='done'>
                    </div>
                  </div>
                  <div class='edit$row[0] edit'>
                    <input type='text' name='edit_name_task' value='$row[1]'>
                    <textarea name='info' placeholder='Write More Informtion About The Task...'>$row[6]</textarea>
                    <select name='status_change'>
                      <option value='done$row[0]'>Done</option>
                      <option value='work$row[0]'>work</option>
                      <option value='del$row[0]'>Delete</option>
                    </select>
                    <input type='submit' value='Send' name='send_edit'>
                  </div>
                  </form>
              ";
              // Code JS For Open The Box Edit
              echo "
              <script>
                let icon_edit$row[0] = document.getElementsByClassName('icon_edit$row[0]')[0];
                let status$row[0] = false;
                icon_edit$row[0].onclick = function() {
                  if (status$row[0] == false) {
                    document.getElementsByClassName('edit$row[0]')[0].style = `
                    display: flex;
                    transform: scaleY(1);`;
                    status$row[0] = true;
                  } else if (status$row[0] == true) {
                    document.getElementsByClassName('edit$row[0]')[0].style = `
                    display: none;
                    transform: scaleY(0);`;
                    status$row[0] = false;
                  }
                }
              </script>
              ";
              if (isset($_POST['send_edit'])) {
                $edit_name_task = $_POST['edit_name_task'];
                if ($_POST['status_change'] == "done$row[0]") {
                  $sql = "UPDATE `tasks` SET `status` = 'done', `time_done` = current_timestamp(), `name` = '$edit_name_task' WHERE `tasks`.`id` = $row[0]";
                  mysqli_query($db, $sql);

                  // Refrech The Page After Change Status
                  header("Refresh:0");
                } elseif ($_POST['status_change'] == "work$row[0]") {
                  $sql = "UPDATE `tasks` SET `name` = '$edit_name_task' WHERE `tasks`.`id` = $row[0]";
                  mysqli_query($db, $sql);

                  header("Refresh:0");
                } elseif ($_POST['status_change'] == "del$row[0]") {
                  $sql = "DELETE FROM tasks WHERE `tasks`.`id` = $row[0]";
                  mysqli_query($db, $sql);

                  header("Refresh:0");
                }
              }
              echo '</div>';
            }
          }
          if ($count_task_work == 0) {
            echo $no_task;
          }
          ?>
        </div>
      </div>
      <div class="done box">
        <h3 class="title_section">Done Tasks</h3>
        <div class="tasks">
          <?php
          foreach ($rows as $row) {
            if ($row[2] == 'done') {
              ++$count_task_done;

              echo "<div class='box' style='font-family: 'Cairo', sans-serif;'>";
              print "
                <div class='info'>
                  <span class='title'>$count_task_done- $row[1]</span>
                  <div class='actions'>
                    <i class='fa-solid fa-pen-to-square icon_edit$row[0] icon'></i>
                    <span class='date'>
                      <span>Time Add: " . time_fun($row[3]) . "</span>
                      <span>Time Done: " . time_fun($row[4]) . "</span>
                      <span>Time End: " . time_fun($row[5]) . "</span>
                    </span>
                    <form action='' method='POST'>
                      <input type='submit' value='Delete' name='send_edit' class='del'>
                  </div>
                </div>
                <div class='edit$row[0] edit'>
                  <input type='text' name='edit_name_task' value='$row[1]'>
                  <textarea name='info' placeholder='Write More Informtion About The Task...'>$row[6]</textarea>
                  <select name='status_change'>
                    <option value='del$row[0]'>Delete</option>
                    <option value='work$row[0]'>work</option>
                  </select>
                  <input type='submit' value='Send' name='send_edit'>
                </div>
                </form>
              ";
              // Code JS For Open The Box Edit
              echo "
              <script>
                let icon_edit$row[0] = document.getElementsByClassName('icon_edit$row[0]')[0];
                let status$row[0] = false;
                icon_edit$row[0].onclick = function() {
                  if (status$row[0] == false) {
                    document.getElementsByClassName('edit$row[0]')[0].style = `
                    display: flex;
                    transform: scaleY(1);`;
                    status$row[0] = true;
                  } else if (status$row[0] == true) {
                    document.getElementsByClassName('edit$row[0]')[0].style = `
                    display: none;
                    transform: scaleY(0);`;
                    status$row[0] = false;
                  }
                }
              </script>
              ";
              // Change Status
              if (isset($_POST['status_change'])) {
                if ($_POST['status_change'] == "work$row[0]") {
                  $sql = "UPDATE `tasks` SET `status` = 'work', `time_done` = NULL WHERE `tasks`.`id` = $row[0]";
                  mysqli_query($db, $sql);

                  // Refrech The Page After Change Status
                  header("Refresh:0");
                } elseif ($_POST['status_change'] == "del$row[0]") {
                  $sql = "DELETE FROM tasks WHERE `tasks`.`id` = $row[0]";
                  mysqli_query($db, $sql);

                  // Refrech The Page After Change Status
                  header("Refresh:0");
                }
              }
              echo '</div>';
            }
          }
          if ($count_task_done == 0) {
            echo $no_task;
          }
          ?>
        </div>
      </div>
    </div>
</body>

</html>