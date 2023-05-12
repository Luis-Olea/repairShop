 <?php
  if (!isset($_SESSION['logged'])) {
    header('location: ../clientMenu.php');
  }
  $client = $_SESSION['client'];
  ?>

 <div class="modal fade modal-lg" id="deleteClient" tabindex="-1" aria-labelledby="deleteClientModal" aria-hidden="true">
   <div class="modal-dialog modal-xl modal-dialog-centered">
     <div class="modal-content">
       <div class="modal-header">
         <h1 class="modal-title fs-5" id="deleteClientModal">Eliminar cliente</h1>
         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <form class="form-horizontal" method="post">
         <div class="modal-body">
           <div class="container bg-8 text-center">
             <div class="panel panel-primary">
               <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                 <symbol id="exclamation-triangle-fill" fill="red" viewBox="0 0 16 16">
                   <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                 </symbol>
               </svg>
               <svg class="bi flex-shrink-0 me-2" width="100" height="100" role="img" aria-label="Danger:">
                 <use xlink:href="#exclamation-triangle-fill" />
               </svg>
               <h4>
                 <br>
                 ¿Quieres eliminar a:
                 <?php echo "$client->clientName $client->clientLastName?"; ?>
               </h4>
               <br>
             </div>
           </div>
         </div>
         <div class="modal-footer">
           <input type="hidden" value="<?= $client->clientId ?>" name="clientId">
           <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
           <button type="submit" class="btn btn-success" name="deleteClient" value="Delete">Eliminar cliente</button>
         </div>
       </form>
     </div>
   </div>
 </div>