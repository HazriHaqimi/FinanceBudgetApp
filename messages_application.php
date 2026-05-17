<?php
      if (!empty($message_erreur) || !empty($message)) {
        ?>
        <!-- **************************************** -->
        <!-- Messages éventuels de l'application      -->
        <div class="ui segment">
          <h1 class="ui header"> Logs </h1>
          <div id="logs">
            <?php
            if (!empty($message_erreur)) {
              echo '<div class="ui red message">' . $message_erreur . "</div>\n";
            }
            if (!empty($message)) {
              echo '<div class="ui green message">' . $message . "</div>\n";
            }
            ?>
          </div>                
        </div>          
        <?php
      }
?>
