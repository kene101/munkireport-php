 		<div class="col-lg-4 col-md-6">

			<div class="panel panel-default">

				<div class="panel-heading" data-container="body" title="Pending installs for this week">

					<h3 class="panel-title"><i class="fa fa-shopping-cart"></i> Pending Installs</h3>
				
				</div>

				<div class="list-group scroll-box">

				<?php
					$mr = new Munkireport_model;
					$pendinginstalls_array = array();
					$week_ago = date('Y-m-d H:i:s', time() - 3600 * 24 * 7);
					$filter = get_machine_group_filter('AND');
					$sql = "SELECT m.serial_number, report_plist 
							FROM munkireport m
							LEFT JOIN reportdata USING (serial_number)
							WHERE pendinginstalls > 0
							$filter
							AND m.timestamp > '$week_ago'";
					// Get compression (fixme: we should be able to read this from the model) 
					$compress = function_exists('gzdeflate');
					
					//loop through all the plists
					foreach($mr->query($sql) as $obj){
		
            			$report_plist = unserialize( $compress ? gzinflate( $obj->report_plist ) : $obj->report_plist );
            			
            			//loop inside the plist to get the pending install and fill the pendinginstalls_array with the display names
                		foreach($report_plist['ItemsToInstall'] AS $pendinginstall){
                			$pendinginstalls_array[] = $pendinginstall['display_name'] . ' ' . $pendinginstall['version_to_install'];
                		}
                		
					}
					//group the updates by count now that the loops are done
					$pendinginstalls_array = array_count_values($pendinginstalls_array);
					arsort($pendinginstalls_array);
				?>
				<?php if( ! $pendinginstalls_array): ?>
					<span class="list-group-item">No updates pending</span>
				<?php endif; ?>
				<?php foreach(array_keys($pendinginstalls_array) as $obj): ?>


					<a href="<?php echo url('module/munkireport/pending#'.$obj); ?>" class="list-group-item">
					<!--//echo first the key names (update name) and then their values (count) -->
                	<?php echo $obj; ?>
                	<span class="badge pull-right"><?php echo $pendinginstalls_array[$obj]; ?></span>
            		</a>

				<?php endforeach; ?>
				</div>

			</div><!-- /panel -->

		</div><!-- /col -->
