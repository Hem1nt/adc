<?php

// load Magento core file
require_once('app/Mage.php');
umask(0);
Mage::app('default');
error_reporting(E_ALL);

// define codepools
$codepools = array('local', 'community');

// define arrays and counters for storing rewrite, override and observers information
$blockRewritesData = array();
$modelRewritesData = array();
$controllerOverridesData = array();
$observersSetData = array();

$i = 0;
$j = 0;
$k = 0;
$l = 0;
$m = 0;
$n = 0;
$o = 0;

//
// start logic
//

// iterate through codepools
foreach($codepools as $dir) {
	if ($codepoolHandle = opendir('app/code/' . $dir)) {
		// load namespaces
		while (false !== ($nameSpace = readdir($codepoolHandle))) {
			if ($nameSpace != "." && $nameSpace != "..") {
	            if ($modules = opendir('app/code/' . $dir . '/' . $nameSpace)) {
	            	// load modules
					while (false !== ($moduleFolder = readdir($modules))) {
				        if ($moduleFolder != "." && $moduleFolder != "..") {
				        	// prepare path for config.xml, prepare counters
				        	$configFilePath = Mage::getBaseDir() . "/app/code/" . $dir . "/" . $nameSpace . "/" . $moduleFolder . "/etc/config.xml";
							$i = 0;
							$j = 0;
							$k = 0;
							$l = 0;
							$m = 0;
							$n = 0;
							$o = 0;

							// if config file exists, load it as a simplexml object
				        	if(file_exists($configFilePath)) {
				        		$configXml = simplexml_load_file($configFilePath);

				        		// check and proceed only if module is active
				        		foreach($configXml->modules as $version) {
				        			$key = (array)$version;
				        			// print_r($key); exit;
				        			foreach($key as $index=>$value) {
				        				$module = (string)$index;
				        			}
				        		}
			        			if(Mage::getConfig()->getModuleConfig($module)->is('active', 'true')) {
				        		
					        		/*
					        		 * @blockRewritesData		Array
					        		 *
					        		 * BLOCK REWRITES SECTION STARTS */
					        		// get block rewrites
					        		if($configXml->global->blocks) {
					        			$blocksNode = $configXml->global->blocks;
					        			if($blocksNode->children()) {
					        				foreach($blocksNode->children() as $blockName => $blockDetails) {
					        					// if the module is rewriting any block, store this information in array
					        					if($blockDetails->rewrite) {
						        					foreach($blockDetails->rewrite->children() as $blockRewrittenWhat => $blockRewrittenByObject) {
						        						$blockRewrittenBy = (array)$blockRewrittenByObject;
						        						$blockRewritesData[$dir][$nameSpace][$moduleFolder][$i] = array($blockRewrittenWhat, $blockRewrittenBy[0]);
						        						$i++;
						        					}
					        					}
					        				}
					        			}
					        		}
					        		/* BLOCK REWRITES SECTION ENDS */

					        		
					        		/*
					        		 * @modelRewritesData		Array
					        		 *
					        		 * MODEL REWRITES SECTION STARTS */
					        		// get model rewrites
					        		if($configXml->global->models) {
					        			$modelsNode = $configXml->global->models;
					        			if($modelsNode->children()) {
					        				foreach($modelsNode->children() as $modelName => $modelDetails) {
					        					// if the module is rewriting any model, store this information in array
					        					if($modelDetails->rewrite) {
						        					foreach($modelDetails->rewrite->children() as $modelRewrittenWhat => $modelRewrittenByObject) {
						        						$modelRewrittenBy = (array)$modelRewrittenByObject;
						        						$modelRewritesData[$dir][$nameSpace][$moduleFolder][$j] = array($modelRewrittenWhat, $modelRewrittenBy[0]);
						        						$j++;
						        					}
					        					}
					        				}
					        			}
					        		}
					        		/* MODEL REWRITES SECTION ENDS */

					        		
					        		/*
					        		 * @controllerOverridesData	Array
					        		 *
					        		 * CONTROLLER OVERRIDES SECTION STARTS */
									// get frontend controller overrides
									if($configXml->frontend->routers) {
					        			$routersNode = $configXml->frontend->routers;
					        			if($routersNode->children()) {
					        				foreach($routersNode->children() as $routerName => $routerDetails) {
					        					if($routerDetails->args->modules) {
					        						// if the module is overriding any controller
						        					foreach($routerDetails->args->modules->children() as $controllerRewrittenWhat => $controllerOverriddenByObject) {
						        						$controllerOverriddenWhat = strval($controllerOverriddenByObject['before']);
						        						if(!$controllerOverriddenWhat) {
						        							$controllerOverriddenWhat = strval($controllerOverriddenByObject['after']);
						        						}
						        						$controllerOverriddenWith = strval($controllerOverriddenByObject[0]);
						        						$controllerOverridesData[$dir]['frontend'][$nameSpace][$moduleFolder][$k] = array($controllerOverriddenWhat, $controllerOverriddenWith);
						        						$k++;
						        					}
					        					}
					        				}
					        			}
					        		}

									// get admin controller overrides
									if($configXml->admin->routers) {
					        			$routersNode = $configXml->admin->routers;
					        			if($routersNode->children()) {
					        				foreach($routersNode->children() as $routerName => $routerDetails) {
					        					if($routerDetails->args->modules) {
					        						// if the module is overriding any controller
						        					foreach($routerDetails->args->modules->children() as $controllerRewrittenWhat => $controllerOverriddenByObject) {
						        						$controllerOverriddenWhat = strval($controllerOverriddenByObject['before']);
						        						if(!$controllerOverriddenWhat) {
						        							$controllerOverriddenWhat = strval($controllerOverriddenByObject['after']);
						        						}
						        						$controllerOverriddenWith = strval($controllerOverriddenByObject[0]);
						        						$controllerOverridesData[$dir]['admin'][$nameSpace][$moduleFolder][$l] = array($controllerOverriddenWhat, $controllerOverriddenWith);
						        						$l++;
						        					}
					        					}
					        				}
					        			}
					        		}
					        		/* CONTROLLER OVERRIDES SECTION ENDS */

					        		
					        		/*
					        		 * @observersData 			Array
					        		 *
					        		 * OBSERVERS SECTION STARTS */
					        		// get frontend observers
					        		if($configXml->frontend->events) {
					        			$eventsNode = $configXml->frontend->events;
					        			if($eventsNode->children()) {
					        				foreach($eventsNode->children() as $eventName => $eventDetails) {
					        					// if the module has set any observers
					        					if($eventDetails->observers) {
						        					foreach($eventDetails->observers->children() as $anObserver=>$observerDetails) {
						        						$observerClass = strval($observerDetails->class);
						        						$observerMethod = strval($observerDetails->method);
						        						$observersData[$dir]['frontend'][$nameSpace][$moduleFolder][$m] = array($eventName, $observerClass, $observerMethod);
						        						$m++;
						        					}
					        					}
					        				}
					        			}
					        		}

					        		// get global observers
					        		if($configXml->global->events) {
					        			$eventsNode = $configXml->global->events;
					        			if($eventsNode->children()) {
					        				foreach($eventsNode->children() as $eventName => $eventDetails) {
					        					// if the module has set any observers
					        					if($eventDetails->observers) {
						        					foreach($eventDetails->observers->children() as $anObserver=>$observerDetails) {
						        						$observerClass = strval($observerDetails->class);
						        						$observerMethod = strval($observerDetails->method);
						        						$observersData[$dir]['global'][$nameSpace][$moduleFolder][$n] = array($eventName, $observerClass, $observerMethod);
						        						$n++;
						        					}
					        					}
					        				}
					        			}
					        		}

					        		// get adminhtml observers
					        		if($configXml->adminhtml->events) {
					        			$eventsNode = $configXml->adminhtml->events;
					        			if($eventsNode->children()) {
					        				foreach($eventsNode->children() as $eventName => $eventDetails) {
					        					// if the module has set any observers
					        					if($eventDetails->observers) {
						        					foreach($eventDetails->observers->children() as $anObserver=>$observerDetails) {
						        						$observerClass = strval($observerDetails->class);
						        						$observerMethod = strval($observerDetails->method);
						        						$observersData[$dir]['adminhtml'][$nameSpace][$moduleFolder][$o] = array($eventName, $observerClass, $observerMethod);
						        						$o++;
						        					}
					        					}
					        				}
					        			}
					        		}
					        		/* OBSERVERS SECTION ENDS */

				        		}
				        	}
				        }
				    }
				}
			}
		}
	}
}

//
// end logic
//

?>

<?php

//
// start display
//

?>

<style type="text/css">
.container { width:1510px; font:12px verdana; }
table { border-spacing:0; border-collapse:collapse; font:12px verdana; }
table td, table th, table tr { padding:0px; text-align:left; vertical-align:top; line-height:2em; }
table th { border-left:1px solid; }
table th:first-child { border-left:none; }
table td, table th { padding-left:6px; }
.headers tr, .frame tr { border:1px solid; }
.first tr, .second tr, .third tr { border:none; border-left:1px solid; border-bottom:1px solid; }
.first tr:last-child, .second tr:last-child, .third tr:last-child { border-bottom:none; }
td.codepools { width:100px; }
th.codepools { width:106px; }
td.sections { width:100px; }
th.sections { width:106px; }
td.namespaces, td.modules { width:160px; }
th.namespaces, th.modules { width:166px; }
.rewrites_what { width:240px; }
.rewrites_with { width:480px; }
td.rewrites_with, td.observer_event, td.observer, td.method { border-left:1px solid; }
.controllers .rewrites_with { width:367px; }
.observers td.codepools, .observers td.sections { width:80px; }
.observers th.codepools, .observers th.sections { width:86px; }
.observers td.namespaces, .observers td.modules { width:130px; }
.observers th.namespaces, .observers th.modules { width:136px; }
.observers .observer_event { width:550px; }
.observers .observer { width:236px; }
.observers .method { width:220px; }
</style>

<?php
/*
 * 
 * Data
 * @$blockRewritesData				Block Rewrites Data
 * @$modelRewritesData				Model Rewrites Data
 * @$controllerOverridesData		Controller Overrides Data
 * @$observersData 					Observers Data
 * 
 * 
 * */
?>

<!-- Main Container -->
<div class="container">
	<h1>REWRITES, OVERRIDES AND OBSERVERS <em>(Only Active Modules)</em></h1>

	<?php
	/*
	 * Data
	 * @$blockRewritesData			Array
	 *
	 * BLOCKS Table */
	?>
	<div class="blocks">
		<h2>BLOCKS</h2><br/>
		<table class="headers">
			<thead>
				<th class="codepools">Codepools</th>
				<th class="namespaces">Namespaces</th>
				<th class="modules">Modules</th>
				<th class="rewrites_what">Rewritten What</th>
				<th class="rewrites_with">Rewritten With</th>
			</thead>
		</table>
		<table class="frame">
			<tbody>
				<?php foreach($blockRewritesData as $codepool=>$namespaces): ?>
					<!-- Codepools -->
					<tr>
						<td class="codepools"><?php echo $codepool ?></td>
						<td>
							<table class="second">
								<?php foreach($namespaces as $namespace=>$modules): ?>
									<tr>
										<!-- Namespaces -->
										<td class="namespaces"><?php echo $namespace ?></td>
										<td>
											<table class="third">
												<?php foreach($modules as $module=>$rewrites): ?>
													<tr>
														<!-- Modules -->
														<td class="modules"><?php echo $module ?></td>
														<td>
															<table class="fourth">
																<?php foreach($rewrites as $rewrite): ?>
																	<tr>
																		<!-- Rewrites -->
																		<td class="rewrites_what"><?php echo $rewrite[0] ?></td>
																		<td class="rewrites_with"><?php echo $rewrite[1] ?></td>
																	</tr>
																<?php endforeach; ?>
															</table>
														</td>
													</tr>
												<?php endforeach; ?>
											</table>
										</td>
									</tr>
								<?php endforeach; ?>
							</table>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<?php
	/*
	 * Data
	 * @$modelRewritesData			Array
	 *
	 * MODELS Table */
	?>
	<div class="models">
		<br/><h2>MODELS</h2><br/>
		<table class="headers">
			<thead>
				<th class="codepools">Codepools</th>
				<th class="namespaces">Namespaces</th>
				<th class="modules">Modules</th>
				<th class="rewrites_what">Rewritten What</th>
				<th class="rewrites_with">Rewritten With</th>
			</thead>
		</table>
		<table class="frame">
			<tbody>
				<?php foreach($modelRewritesData as $codepool=>$namespaces): ?>
					<!-- Codepools -->
					<tr>
						<td class="codepools"><?php echo $codepool ?></td>
						<td>
							<table class="second">
								<?php foreach($namespaces as $namespace=>$modules): ?>
									<tr>
										<!-- Namespaces -->
										<td class="namespaces"><?php echo $namespace ?></td>
										<td>
											<table class="third">
												<?php foreach($modules as $module=>$rewrites): ?>
													<tr>
														<!-- Modules -->
														<td class="modules"><?php echo $module ?></td>
														<td>
															<table>
																<?php foreach($rewrites as $rewrite): ?>
																	<tr class="fourth">
																		<!-- Rewrites -->
																		<td class="rewrites_what"><?php echo $rewrite[0] ?></td>
																		<td class="rewrites_with"><?php echo $rewrite[1] ?></td>
																	</tr>
																<?php endforeach; ?>
															</table>
														</td>
													</tr>
												<?php endforeach; ?>
											</table>
										</td>
									</tr>
								<?php endforeach; ?>
							</table>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<?php
	/*
	 * Data
	 * @$controllerOverridesData	Array
	 *
	 * CONTROLLERS Table */
	?>
	<div class="controllers">
		<br/><h2>CONTROLLERS</h2><br/>
		<table class="headers">
			<thead>
				<th class="codepools">Codepools</th>
				<th class="sections">Code Sections</th>
				<th class="namespaces">Namespaces</th>
				<th class="modules">Modules</th>
				<th class="rewrites_what">Rewritten What</th>
				<th class="rewrites_with">Rewritten With</th>
			</thead>
		</table>
		<table class="frame">
			<tbody>
				<?php foreach($controllerOverridesData as $codepool=>$sections): ?>
					<!-- Codepools -->
					<tr>
						<td class="codepools"><?php echo $codepool ?></td>
						<td>
							<table class="first">
								<?php foreach($sections as $section=>$namespaces): ?>
									<!-- Code Sections -->
									<tr>
										<td class="sections"><?php echo $section ?></td>
										<td>
											<table class="second">
												<?php foreach($namespaces as $namespace=>$modules): ?>
													<tr>
														<!-- Namespaces -->
														<td class="namespaces"><?php echo $namespace ?></td>
														<td>
															<table class="third">
																<?php foreach($modules as $module=>$rewrites): ?>
																	<tr>
																		<!-- Modules -->
																		<td class="modules"><?php echo $module ?></td>
																		<td>
																			<table class="fourth">
																				<?php foreach($rewrites as $rewrite): ?>
																					<tr>
																						<!-- Rewrites -->
																						<td class="rewrites_what"><?php echo $rewrite[0] ?></td>
																						<td class="rewrites_with"><?php echo $rewrite[1] ?></td>
																					</tr>
																				<?php endforeach; ?>
																			</table>
																		</td>
																	</tr>
																<?php endforeach; ?>
															</table>
														</td>
													</tr>
												<?php endforeach; ?>
											</table>
										</td>
									</tr>
								<?php endforeach; ?>
							</table>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<?php
	/*
	 * Data
	 * @$observersData 				Array
	 *
	 * OBSERVERS Table */
	?>
	<div class="observers">
		<br/><h2>OBSERVERS</h2><br/>
		<table class="headers">
			<thead>
				<th class="codepools">Codepools</th>
				<th class="sections">Code Sections</th>
				<th class="namespaces">Namespaces</th>
				<th class="modules">Modules</th>
				<th class="observer_event">Observer Event</th>
				<th class="observer">Observer</th>
				<th class="method">Method</th>
			</thead>
		</table>
		<table class="frame">
			<tbody>
				<?php foreach($observersData as $codepool=>$sections): ?>
					<!-- Codepools -->
					<tr>
						<td class="codepools"><?php echo $codepool ?></td>
						<td>
							<table class="first">
								<?php foreach($sections as $section=>$namespaces): ?>
									<!-- Code Sections -->
									<tr>
										<td class="sections"><?php echo $section ?></td>
										<td>
											<table class="second">
												<?php foreach($namespaces as $namespace=>$modules): ?>
													<tr>
														<!-- Namespaces -->
														<td class="namespaces"><?php echo $namespace ?></td>
														<td>
															<table class="third">
																<?php foreach($modules as $module=>$rewrites): ?>
																	<tr>
																		<!-- Modules -->
																		<td class="modules"><?php echo $module ?></td>
																		<td>
																			<table class="fourth">
																				<?php foreach($rewrites as $rewrite): ?>
																					<tr>
																						<!-- Rewrites -->
																						<td class="observer_event"><?php echo $rewrite[0] ?></td>
																						<td class="observer"><?php echo $rewrite[1] ?></td>
																						<td class="method"><?php echo $rewrite[2] ?></td>
																					</tr>
																				<?php endforeach; ?>
																			</table>
																		</td>
																	</tr>
																<?php endforeach; ?>
															</table>
														</td>
													</tr>
												<?php endforeach; ?>
											</table>
										</td>
									</tr>
								<?php endforeach; ?>
							</table>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

</div>
<br/><br/><br/>
<?php
//
// end display
//
?>