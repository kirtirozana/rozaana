<?php
class Textlocal_SMSNotifications_Model_System_Config_Source_Logdetails
{
    public $superglobals = array(
        '$_REQUEST'
    );
  /**
  * Options getter
  *
  * @return array
  */

    public function toOptionArray()
    { 
      $settings = Mage::helper('smsnotifications/data')->getSettings(); 
      $Textlocl = new Textlocal_SMSNotifications_Model_Textlocal(false, false, $settings['sms_auth_token']);

        $mintime = strtotime('-1 month'); // Get sends between a month ago 
        $maxtime = time(); // and now
        $limit = 1000;
        $start = 0;
        $response = $Textlocl->getAPIMessageHistory($start, $limit, $mintime, $maxtime);
       
        $messag = $response->messages; 
        $jsonData = Mage::helper('core')->jsonEncode($messag);

     if (filter_input(INPUT_GET, 'export')) 
       {
         require_once 'app/Mage.php';
         Mage::app(Mage_Core_Model_App::ADMIN_STORE_ID);

          $outputFile =  "loghistory_" . date('Y-m-d') . ".csv";
          $filepath =  'var\export'.$outputFile;     
       
       $messageexport = array();
       foreach ($messag as $key => $value) {
        $datetime=$value->datetime;
        $number =$value->number;
        $sender= $value->sender;
        $message=$value->content;
        $status=$value->status;
        $messageexport[] = array(
            'Datetime' =>$datetime ,
            'Number' => $number,
            'Sender' => $sender,
            'Message' => $message,
            'Status' => $status
         );
      }
       
         $write = fopen($filepath, 'w');
              
              $heading = false;
       if(!empty($messageexport))
           foreach($messageexport as $fields) {
            
            if(!$heading) {
              // output the column headings
              fputcsv($write, array_keys($fields));
              $heading = true;
           }
           fputcsv($write, array_values($fields));
       }
              fclose($write);
              header( 'Content-Type:application/octet-stream' );
              header( 'Content-Disposition:filename='.$outputFile );
              header( 'Content-Length:' . filesize( $filepath ) );
              readfile( $filepath ); 
              unlink( $filepath );
   }
 ?>
       <div class="lg_history" id="popup-mpdal" style="display:none">
       <div class="popop-model-inner-wrap">  <div class="close-popop"><span>+</span></div>
     
      <html>
      <head>

         <link rel="stylesheet prefetch" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.min.css">
         <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
         <script src="https://cdnjs.cloudflare.com/ajax/libs/twbs-pagination/1.4.1/jquery.twbsPagination.min.js"></script>
      </head>
      <body>

<table id="textlocal_log" class="table table-bordered table table-hover" cellspacing="0" width="100%">
     <colgroup><col><col><col></colgroup>
      <thead>
      <tr>
            <th width="20%">Datetime</th>
      <th width="20%">Number</th>
      <th width="20%">Sender</th>
      <th width="30%">Message</th>
      <th width="5%">Status</th>
      </tr>
      </thead>
      <tbody id="log_history">
      </tbody>
</table>
<div id="pager">
    <ul id="pagination" class="pagination-sm"></ul>
</div>

  <form>
        <input type="submit" name="export" value="Export" />
  </form>

   
<script type="text/javascript">
  var data = <?php echo json_encode($messag)  ?>;
console.log(data);
  var PerPagerec = 10;
  var RecordsTotal = data.length;
  var Pages = Math.ceil(RecordsTotal / PerPagerec);
   totalRecords = 0,
   recPerPage = 10,
   page = 1,

  jQuery('#pagination').twbsPagination({
            totalPages: Pages,
           visiblePages: 20,
           next: 'Next',
           prev: 'Prev',

  onPageClick: function (event, page, recored) {
                  records = data;
                 totalRecords = records.length;
                 totalPages = Math.ceil(totalRecords / recPerPage);
                     //console.log(totalRecords);
                  displayRecordsIndex = Math.max(page - 1, 0) * recPerPage;
                  endRec = (displayRecordsIndex) + recPerPage;
                 displayRecords = records.slice(displayRecordsIndex, endRec);
                  var tr;
              jQuery('#log_history').html('');
                for (var i = 0; i < displayRecords.length; i++) {
              tr = jQuery('<tr/>');
              tr.append("<td>" + displayRecords[i].datetime + "</td>");
              tr.append("<td>" + displayRecords[i].number + "</td>");
              tr.append("<td>" + displayRecords[i].sender + "</td>");
              tr.append("<td>" + displayRecords[i].content + "</td>");
              tr.append("<td>" + displayRecords[i].status + "</td>");
              jQuery('#log_history').append(tr);
      }
      }
 });

</script>

</body>

      </div> 
      </div>

<?php 
       }
}

 