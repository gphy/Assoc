
<!-- onglet Recherche --> 
<div class="container">

		<div class="row">
			 <div class="col-md-1">	
						<?php 	
		echo $this->form->create("maj_op",array("id"=>"form"));
				echo $this->form->input("test",array("type"=>"hidden","value"=>0,"id"=>"Hide")); ?>
				<?php echo $this->form->submit("<<",array("class"=>"btn btn-lg btn-default","onclick"=>"chgPage(-9999999);")); ?> 
				</div>
				<div class="col-md-1">	
				<?php echo $this->form->submit("<",array("class"=>"btn btn-lg btn-default","onclick"=>"chgPage(".-$size.");"));  ?>
				</div>
				<div class="col-md-2">	
				<?php echo $this->form->submit("Enregistrer",array("class"=>"btn btn-lg btn-default"));  ?>
				</div>
				<div class="col-md-1">	
				<?php echo $this->form->submit(">",array("class"=>"btn btn-lg btn-default","onclick"=>"chgPage(".$size.");"));  ?>
				</div>
				<div class="col-md-1">	
				<?php echo $this->form->submit(">>",array("class"=>"btn btn-lg btn-default","onclick"=>"chgPage(9999999);")); ?>
			</div>
			
		</div>	
		<div class="row">
	    	<div class="table-responsive">
	    		<table class="table table-bordered">	
	    			<tr>
	    				<th>  </th>
	    				<th> Libellé </th>
	    				<th> Paiement </th>
	    				<th> Date op </th>

	    				<th> <?php echo $this->form->input("date Depot",array("class"=>"form-control")); ?> </th>
	    				<th> <?php echo $this->form->input("date Valeur",array("class"=>"form-control")); ?>  </th>
	    			</tr>
	    			<?php
	    			
	    			for ($a =$decal;$a <$decal+$size&&$a<$nb_rep;$a++) {
	    				$value = $op[$a];
	    				echo "<tr>  ";
	    				echo '<TD> '. $this->Html->link('<span class="btn btn-danger">Supprimer</span>',array("controller"=>"Comptas","action"=>"deleteOp",$value["bal_vue_e401_operations"]["ComptabiliteId"],$decal),array('escape' => false)) .'</TD> '; 
	    				echo "<td >". $value["bal_vue_e401_operations"]["Libelle"] ."</td> ";
	    				echo "<td >". $value["bal_vue_e401_operations"]["MoyenDePaiement"] ."</td> ";
	    				echo "<td >". $value["bal_vue_e401_operations"]["DateOperation"] ."</td> ";
	    				if ($value["bal_vue_e401_operations"]["DateDepot"]=="")
	    				{
	    					echo "<td >". $this->form->input("Za".$value["bal_vue_e401_operations"]["ComptabiliteId"],array("type"=>"checkbox","label"=>false,"class"=>"form-control")) ."</td> ";
	    				}
	    				else
	    				{
	    					echo "<td >".$this->form->input("Za".$value["bal_vue_e401_operations"]["ComptabiliteId"],array("readonly"=>"true","label"=>false,"class"=>"form-control","value"=>$value["bal_vue_e401_operations"]["DateDepot"])) ."</td> ";

	    				}
	    				if ($value["bal_vue_e401_operations"]["DateValeur"]=="")
	    				{
							echo "<td >". $this->form->input("Zb".$value["bal_vue_e401_operations"]["ComptabiliteId"],array("type"=>"checkbox","label"=>false,"class"=>"form-control")) ."</td> ";
	    				}
	    				else
	    				{
	    					echo "<td >".$this->form->input("Zb".$value["bal_vue_e401_operations"]["ComptabiliteId"],array("readonly"=>"true","label"=>false,"class"=>"form-control","value"=>$value["bal_vue_e401_operations"]["DateValeur"])) ."</td> ";
	    					
	    				}	    		
	    				echo "</tr>  ";

	    			}

	    			?>
	    		</table>
	  	
     		</div>	
	</div>

</div>
<script> function chgPage(val)
	{
	 
	 document.getElementById("Hide" ).value = val;
		
	} 
</script>
