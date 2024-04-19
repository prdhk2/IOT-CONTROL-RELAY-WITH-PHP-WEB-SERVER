<?php
require "template.php";

$led_state = "";

// Periksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Periksa apakah led_state telah diberikan nilai dari form
    if (!empty($_POST["led_state"])) {
        $led_state = $_POST["led_state"];
    }
}
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>Control Relay</title>

    <link rel="stylesheet" href="style/css/styles.css"> <!-- custom styles -->

</head>
    <body>
        <h3><u>Data ON / OFF Relay</u></h3>
        <div class="container">
            <div class="search-form" style="text-align: right;">
                <form method="POST" action="">
                    <div class="row mt-3">
                        <div class="col-6">   
                        </div>
                        <div class="col-6 d-inline-flex" style="justify-content:right;">
                            <div class="form-group">
                                <select name="led_state" id="led_state" class="form-control">
                                    <option value="">Filter Status LED</option>
                                    <option value="LED_01" <?php if ($led_state == 'LED_01') { echo "selected"; } ?>>LED 01 ON</option>
                                    <option value="LED_02" <?php if ($led_state == 'LED_02') { echo "selected"; } ?>>LED 02 ON</option>
                                    <option value="LED_01_OFF" <?php if ($led_state == 'LED_01_OFF') { echo "selected"; } ?>>LED 01 OFF</option>
                                    <option value="LED_02_OFF" <?php if ($led_state == 'LED_02_OFF') { echo "selected"; } ?>>LED 02 OFF</option>
                                </select>
                            </div>
                            <div class="form-group ml-2">
                                <input type="date" name="filter_date" id="filter_date" class="form-control" value="<?php echo isset($_POST['filter_date']) ? $_POST['filter_date'] : ''; ?>">
                            </div>
                            <div class="form-group ml-2">
                                <button id="search" name="search" class="btn btn-warning">Cari</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Form untuk filter data berdasarkan status LED -->
    <!-- Tabel untuk menampilkan data -->
    <table class="styled-table" id="table_id">
      <thead>
        <tr>
          <th>NO</th>
          <th>ID</th>
          <th>BOARD</th>
          <th>LED 01</th>
          <th>LED 02</th>
          <th>TIME</th>
          <th>DATE (dd-mm-yyyy)</th>
        </tr>
      </thead>
      <tbody id="tbody_table_record">
        <?php
            require "database.php";
            
            // Inisialisasi variabel filter
            $filter_date = "";
            $sql_filter = "";

            // Cek apakah ada permintaan pencarian
            if (isset($_POST['search'])) {
                // Filter berdasarkan status LED yang dipilih
                if (!empty($_POST['led_state'])) {
                    if ($_POST['led_state'] == 'LED_01') {
                        $sql_filter .= " AND LED_01 = 'ON'";
                    } elseif ($_POST['led_state'] == 'LED_02') {
                        $sql_filter .= " AND LED_02 = 'ON'";
                    } elseif ($_POST['led_state'] == 'LED_01_OFF') {
                        $sql_filter .= " AND LED_01 = 'OFF'";
                    } elseif ($_POST['led_state'] == 'LED_02_OFF') {
                        $sql_filter .= " AND LED_02 = 'OFF'";
                    }
                }
                
                // Filter berdasarkan tanggal
                if (!empty($_POST['filter_date'])) {
                    $filter_date = $_POST['filter_date'];
                    $sql_filter .= " AND date = '$filter_date'";
                }
            }

            // Kueri SQL
            $sql = "SELECT * FROM data_relay WHERE 1 $sql_filter ORDER BY date DESC, time DESC";

            // Eksekusi kueri
            $pdo = Database::connect();
            $num = 0;
            foreach ($pdo->query($sql) as $row) {
                // Tampilkan data dalam tabel
                $date = date_create($row['date']);
                $dateFormat = date_format($date,"d-m-Y");
                $num++;
                echo '<tr>';
                echo '<td>'. $num . '</td>';
                echo '<td class="bdr">'. $row['id'] . '</td>';
                echo '<td class="bdr">'. $row['board'] . '</td>';
                echo '<td class="bdr">'. $row['LED_01'] . '</td>';
                echo '<td class="bdr">'. $row['LED_02'] . '</td>';
                echo '<td class="bdr">'. $row['time'] . '</td>';
                echo '<td>'. $dateFormat . '</td>';
                echo '</tr>';
            }
            Database::disconnect();
        ?>

      </tbody>
    </table>

    <br>

    <div class="btn-group">
      <button class="button" id="btn_prev" onclick="prevPage()">Prev</button>
      <button class="button" id="btn_next" onclick="nextPage()">Next</button>
      <div style="display: inline-block; position:relative; border-radius:.25rem; border: 0px solid #e3e3e3; float: center; margin-left: 2px;;">
        <p style="position:relative; font-size: 14px;"> Table : <span id="page"></span></p>
      </div>
      <select name="number_of_rows" id="number_of_rows">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
      <button class="button" id="btn_apply" onclick="apply_Number_of_Rows()">Apply</button>
    </div>

    <br>
    
    <script>
      //------------------------------------------------------------
      var current_page = 1;
      var records_per_page = 10;
      var l = document.getElementById("table_id").rows.length
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function apply_Number_of_Rows() {
        var x = document.getElementById("number_of_rows").value;
        records_per_page = x;
        changePage(current_page);
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function prevPage() {
        if (current_page > 1) {
            current_page--;
            changePage(current_page);
        }
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function nextPage() {
        if (current_page < numPages()) {
            current_page++;
            changePage(current_page);
        }
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function changePage(page) {
        var btn_next = document.getElementById("btn_next");
        var btn_prev = document.getElementById("btn_prev");
        var listing_table = document.getElementById("table_id");
        var page_span = document.getElementById("page");
       
        // Validate page
        if (page < 1) page = 1;
        if (page > numPages()) page = numPages();

        [...listing_table.getElementsByTagName('tr')].forEach((tr)=>{
            tr.style.display='none'; // reset all to not display
        });
        listing_table.rows[0].style.display = ""; // display the title row

        for (var i = (page-1) * records_per_page + 1; i < (page * records_per_page) + 1; i++) {
          if (listing_table.rows[i]) {
            listing_table.rows[i].style.display = ""
          } else {
            continue;
          }
        }
          
        page_span.innerHTML = page + "/" + numPages() + " (Total Number of Rows = " + (l-1) + ") | Number of Rows : ";
        
        if (page == 0 && numPages() == 0) {
          btn_prev.disabled = true;
          btn_next.disabled = true;
          return;
        }

        if (page == 1) {
          btn_prev.disabled = true;
        } else {
          btn_prev.disabled = false;
        }

        if (page == numPages()) {
          btn_next.disabled = true;
        } else {
          btn_next.disabled = false;
        }
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function numPages() {
        return Math.ceil((l - 1) / records_per_page);
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      window.onload = function() {
        var x = document.getElementById("number_of_rows").value;
        records_per_page = x;
        changePage(current_page);
      };
      //------------------------------------------------------------
    </script>
  </body>
</html>