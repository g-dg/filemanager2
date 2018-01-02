<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === Session::get('_csrf_token')) {
	if (isset($_POST['admin'], $_POST['action'])) {
		switch ($_POST['admin']) {

			case 'users':
				switch ($_POST['action']) {

					case 'create':
						if (isset($_POST['name'], $_POST['password'], $_POST['type'], $_POST['comment'])) {
							switch ($_POST['type']) {
								case 'administrator':
									$type = Users::USER_TYPE_ADMIN;
									break;
								case 'standard':
									$type = Users::USER_TYPE_STANDARD;
									break;
								case 'guest':
									$type = Users::USER_TYPE_GUEST;
									break;
								default:
									http_response_code(400);
									exit('There was a problem with the request.');
									break;
							}
							$enabled = !(!isset($_POST['enabled']) || $_POST['enabled'] == 'false');
							if (!Users::create($_POST['name'], $_POST['password'], $type, $enabled, $_POST['comment'])) {
								http_response_code(500);
								exit('Could not create user.');
							}
						} else {
							http_response_code(400);
							exit('There was a problem with the request.');
						}
						break;


					case 'get_all':
						$user_array = [];
						$user_ids = Users::getAll();
						foreach ($user_ids as $user_id) {
							$user_record = [];

							$user_record['id'] = $user_id;

							$user_record['name'] = Users::getName($user_id);

							switch (Users::getType($user_id)) {
								case Users::USER_TYPE_ADMIN:
									$user_record['type'] = 'administrator';
									break;
								case Users::USER_TYPE_STANDARD:
									$user_record['type'] = 'standard';
									break;
								case Users::USER_TYPE_GUEST:
									$user_record['type'] = 'guest';
									break;
							}

							$user_record['enabled'] = Users::getEnabled($user_id);

							//$user_record['comment'] = Users::getComment($user_id);

							$user_array[] = $user_record;
						}
						header('Content-type: text/json');
						echo json_encode($user_array);
						break;


					case 'get':
						if (isset($_POST['user_id'])) {
							$user_record = [];

							$user_record['id'] = $_POST['user_id'];

							$user_record['name'] = Users::getName($_POST['user_id']);

							switch (Users::getType($_POST['user_id'])) {
								case Users::USER_TYPE_ADMIN:
									$user_record['type'] = 'administrator';
									break;
								case Users::USER_TYPE_STANDARD:
									$user_record['type'] = 'standard';
									break;
								case Users::USER_TYPE_GUEST:
									$user_record['type'] = 'guest';
									break;
							}

							$user_record['enabled'] = Users::getEnabled($_POST['user_id']);

							$user_record['comment'] = Users::getComment($_POST['user_id']);

							$user_record['can_delete'] = $user_record['can_disable'] = $user_record['can_demote'] = ($_POST['user_id'] != Auth::getCurrentUserId());

							header('Content-type: text/json');
							echo json_encode($user_record);
						} else {
							http_response_code(400);
							exit('There was a problem with the request.');
						}
						break;


					case 'set':
						if (isset($_POST['user_id'], $_POST['set'], $_POST['value'])) {
							switch ($_POST['set']) {

								case 'name':
									if (!Users::setName($_POST['user_id'], $_POST['value'])) {
										http_response_code(500);
										exit('Could not set username.');
									}
									break;


								case 'password':
									if (!Users::setPassword($_POST['user_id'], $_POST['value'])) {
										http_response_code(500);
										exit('Could not set password.');
									}
									break;


								case 'type':
									if ($_POST['user_id'] != Auth::getCurrentUserId()) {
										switch ($_POST['value']) {
											case 'administrator':
												$account_type = Users::USER_TYPE_ADMIN;
												break;
											case 'standard':
												$account_type = Users::USER_TYPE_STANDARD;
												break;
											case 'guest':
												$account_type = Users::USER_TYPE_GUEST;
												break;
											default:
												http_response_code(400);
												exit('There was a problem with the request.');
												break;
										}
										if (!Users::setType($_POST['user_id'], $account_type)) {
											http_response_code(500);
											exit('Could not set account type.');
										}
									} else {
										http_response_code(500);
										exit('Could not set user type.');
									}
									break;


								case 'enabled':
									if ($_POST['user_id'] != Auth::getCurrentUserId()) {
										switch ($_POST['value']) {
											case 'true':
												$enabled = true;
												break;
											case 'false':
												$enabled = false;
												break;
											default:
												http_response_code(500);
												exit('Could not enable/disable user.');
												break;
										}
										if (!Users::setEnabled($_POST['user_id'], $enabled)) {
											http_response_code(500);
											exit('Could not enable/disable user.');
										}
									} else {
										http_response_code(500);
										exit('Could not enable/disable user.');
									}
									break;


								case 'comment':
									if (!Users::setComment($_POST['user_id'], $_POST['value'])) {
										http_response_code(500);
										exit('Could not set comment.');
									}
									break;


								default:
									http_response_code(400);
									exit('There was a problem with the request.');
									break;
							}
						} else {
							http_response_code(400);
							exit('There was a problem with the request.');
						}
						break;


					case 'delete':
						if (isset($_POST['user_id']) && $_POST['user_id'] != Auth::getCurrentUserId()) {
							if (!Users::delete($_POST['user_id'])) {
								http_response_code(500);
								exit('Could not delete user.');
							}
						} else {
							http_response_code(400);
							exit('There was a problem with the request.');
						}
						break;


					default:
						http_response_code(400);
						exit('There was a problem with the request.');
						break;
				}
				break;


			case 'users_groups':
				switch ($_POST['action']) {

					case 'get_users':
						$users = [];
						foreach (Users::getAll() as $user_id) {
							$users[] = ['id' => $user_id, 'name' => Users::getName($user_id)];
						}
						header('Content-type: text/json');
						echo json_encode($users);
						break;


					case 'get_groups':
						if (isset($_POST['user_id'])) {
							$groups = [];
							foreach (Groups::getAll() as $group_id) {
								$groups[] = ['id' => $group_id, 'name' => Groups::getName($group_id), 'member' => Groups::userInGroup($group_id, $_POST['user_id'])];
							}
							header('Content-type: text/json');
							echo json_encode($groups);
						} else {
							http_response_code(400);
							exit('There was a problem with the request.');
						}
						break;


					case 'set_membership':
						if (isset($_POST['user_id'], $_POST['group_id'], $_POST['in_group'])) {
							switch ($_POST['in_group']) {
								case 'true':
									if (!Groups::addUser($_POST['group_id'], $_POST['user_id'])) {
										http_response_code(500);
										exit('Could not add user to group.');
									}
									break;
								case 'false':
									if (!Groups::removeUser($_POST['group_id'], $_POST['user_id'])) {
										http_response_code(500);
										exit('Could not remove user from group.');
									}
									break;
								default:
									http_response_code(400);
									exit('There was a problem with the request.');
							}
						} else {
							http_response_code(400);
							exit('There was a problem with the request.');
						}
						break;


					default:
						http_response_code(400);
						exit('There was a problem with the request.');
						break;
				}
				break;


			case 'groups':
				switch ($_POST['action']) {

					case 'create':
						if (isset($_POST['name'], $_POST['comment'])) {
							$enabled = !(!isset($_POST['enabled']) || $_POST['enabled'] == 'false');
							if (!Groups::create($_POST['name'], $enabled, $_POST['comment'])) {
								http_response_code(500);
								exit('Could not create user.');
							}
						} else {
							http_response_code(400);
							exit('There was a problem with the request.');
						}
						break;


					case 'get_all':
						$group_array = [];
						$group_ids = Groups::getAll();
						foreach ($group_ids as $group_id) {
							$group_record = [];

							$group_record['id'] = $group_id;

							$group_record['name'] = Groups::getName($group_id);

							$group_record['enabled'] = Groups::getEnabled($group_id);

							//$group_record['comment'] = Groups::getComment($group_id);

							$group_array[] = $group_record;
						}
						header('Content-type: text/json');
						echo json_encode($group_array);
						break;


					case 'get':
						if (isset($_POST['group_id'])) {
							$group_record = [];

							$group_record['id'] = $_POST['group_id'];

							$group_record['name'] = Groups::getName($_POST['group_id']);

							$group_record['enabled'] = Groups::getEnabled($_POST['group_id']);

							$group_record['comment'] = Groups::getComment($_POST['group_id']);

							header('Content-type: text/json');
							echo json_encode($group_record);
						} else {
							http_response_code(400);
							exit('There was a problem with the request.');
						}
						break;


					case 'set':
						if (isset($_POST['group_id'], $_POST['set'], $_POST['value'])) {
							switch ($_POST['set']) {

								case 'name':
									if (!Groups::setName($_POST['group_id'], $_POST['value'])) {
										http_response_code(500);
										exit('Could not set name.');
									}
									break;


								case 'enabled':
									switch ($_POST['value']) {
										case 'true':
											$enabled = true;
											break;
										case 'false':
											$enabled = false;
											break;
										default:
											http_response_code(500);
											exit('Could not enable/disable group.');
											break;
									}
									if (!Groups::setEnabled($_POST['group_id'], $enabled)) {
										http_response_code(500);
										exit('Could not enable/disable groups.');
									}
									break;


								case 'comment':
									if (!Groups::setComment($_POST['group_id'], $_POST['value'])) {
										http_response_code(500);
										exit('Could not set comment.');
									}
									break;


								default:
									http_response_code(400);
									exit('There was a problem with the request.');
									break;
							}
						} else {
							http_response_code(400);
							exit('There was a problem with the request.');
						}
						break;


					case 'delete':
						if (isset($_POST['group_id'])) {
								if (!Groups::delete($_POST['group_id'])) {
									http_response_code(500);
									exit('Could not delete group.');
								}
							} else {
								http_response_code(400);
								exit('There was a problem with the request.');
							}
						break;


					default:
						http_response_code(400);
						exit('There was a problem with the request.');
						break;
				}
				break;


			case 'shares_groups':
				switch ($_POST['action']) {

					case 'get_shares':
						$shares = [];
						foreach (Shares::getAll() as $share_id) {
							$shares[] = ['id' => $share_id, 'name' => Shares::getName($share_id)];
						}
						header('Content-type: text/json');
						echo json_encode($shares);
						break;


					case 'get_groups':
						if (isset($_POST['share_id'])) {
							$groups = [];
							foreach (Groups::getAll() as $group_id) {
								$groups[] = [
									'id' => $group_id,
									'name' => Groups::getName($group_id),
									'can_read' => Groups::shareInGroup($group_id, $_POST['share_id']),
									'can_write' => Groups::getShareWritable($group_id, $_POST['share_id'])
								];
							}
							header('Content-type: text/json');
							echo json_encode($groups);
						} else {
							http_response_code(400);
							exit('There was a problem with the request.');
						}
						break;


					case 'set_readable':
						if (isset($_POST['share_id'], $_POST['group_id'], $_POST['readable'])) {
							switch ($_POST['readable']) {
								case 'true':
									if (!Groups::addShare($_POST['group_id'], $_POST['share_id'])) {
										http_response_code(500);
										exit('Could not add share to group.');
									}
									break;
								case 'false':
									if (!Groups::removeShare($_POST['group_id'], $_POST['share_id'])) {
										http_response_code(500);
										exit('Could not remove share from group.');
									}
									break;
								default:
									http_response_code(400);
									exit('There was a problem with the request.');
							}
						} else {
							http_response_code(400);
							exit('There was a problem with the request.');
						}
						break;


					case 'set_writable':
						if (isset($_POST['share_id'], $_POST['group_id'], $_POST['writable'])) {
							switch ($_POST['writable']) {
								case 'true':
									if (!Groups::setShareWritable($_POST['group_id'], $_POST['share_id'], true)) {
										http_response_code(500);
										exit('Could not set share read-write.');
									}
									break;
								case 'false':
									if (!Groups::setShareWritable($_POST['group_id'], $_POST['share_id'], false)) {
										http_response_code(500);
										exit('Could not set share read-only.');
									}
									break;
								default:
									http_response_code(400);
									exit('There was a problem with the request.');
							}
						} else {
							http_response_code(400);
							exit('There was a problem with the request.');
						}
						break;


					default:
						http_response_code(400);
						exit('There was a problem with the request.');
						break;
				}
				break;


			case 'shares':
				switch ($_POST['action']) {

					case 'create':
						if (isset($_POST['name'], $_POST['path'], $_POST['comment'])) {
							$enabled = !(!isset($_POST['enabled']) || $_POST['enabled'] == 'false');
							if (!Shares::create($_POST['name'], $_POST['path'], $enabled, $_POST['comment'])) {
								http_response_code(500);
								exit('Could not create share.');
							}
						} else {
							http_response_code(400);
							exit('There was a problem with the request.');
						}
						break;


					case 'get_all':
						$share_array = [];
						$share_ids = Shares::getAll();
						foreach ($share_ids as $share_id) {
							$share_record = [];

							$share_record['id'] = $share_id;

							$share_record['name'] = Shares::getName($share_id);

							//$share_record['path'] = Shares::getPath($share_id);

							$share_record['enabled'] = Shares::getEnabled($share_id);

							//$share_record['comment'] = Shares::getComment($share_id);

							$share_array[] = $share_record;
						}
						header('Content-type: text/json');
						echo json_encode($share_array);
						break;


					case 'get':
						if (isset($_POST['share_id'])) {
							$share_record = [];

							$share_record['id'] = $_POST['share_id'];

							$share_record['name'] = Shares::getName($_POST['share_id']);

							$share_record['path'] = Shares::getPath($_POST['share_id']);

							$share_record['enabled'] = Shares::getEnabled($_POST['share_id']);

							$share_record['comment'] = Shares::getComment($_POST['share_id']);

							header('Content-type: text/json');
							echo json_encode($share_record);
						} else {
							http_response_code(400);
							exit('There was a problem with the request.');
						}
						break;


					case 'set':
						if (isset($_POST['share_id'], $_POST['set'], $_POST['value'])) {
							switch ($_POST['set']) {

								case 'name':
									if (!Shares::setName($_POST['share_id'], $_POST['value'])) {
										http_response_code(500);
										exit('Could not set name.');
									}
									break;


								case 'path':
									if (!Shares::setPath($_POST['share_id'], $_POST['value'])) {
										http_response_code(500);
										exit('Could not set path.');
									}
									break;


								case 'enabled':
									switch ($_POST['value']) {
										case 'true':
											$enabled = true;
											break;
										case 'false':
											$enabled = false;
											break;
										default:
											http_response_code(500);
											exit('Could not enable/disable share.');
											break;
									}
									if (!Shares::setEnabled($_POST['share_id'], $enabled)) {
										http_response_code(500);
										exit('Could not enable/disable shares.');
									}
									break;


								case 'comment':
									if (!Shares::setComment($_POST['share_id'], $_POST['value'])) {
										http_response_code(500);
										exit('Could not set comment.');
									}
									break;


								default:
									http_response_code(400);
									exit('There was a problem with the request.');
									break;
							}
						} else {
							http_response_code(400);
							exit('There was a problem with the request.');
						}
						break;


					case 'delete':
						if (isset($_POST['share_id'])) {
								if (!Shares::delete($_POST['share_id'])) {
									http_response_code(500);
									exit('Could not delete share.');
								}
							} else {
								http_response_code(400);
								exit('There was a problem with the request.');
							}
						break;


					default:
						http_response_code(400);
						exit('There was a problem with the request.');
						break;
				}
				break;


			case 'global_settings':
				switch ($_POST['action']) {

					case 'get_all':
						$settings = [];
						foreach (GlobalSettings::getAll() as $setting_key) {
							$settings[] = ['key' => $setting_key, 'value' => GlobalSettings::get($setting_key, '')];
						}
						header('Content-type: text/json');
						echo json_encode($settings);
						break;


					case 'set':
						if (isset($_POST['key'], $_POST['value'])) {
							GlobalSettings::set($_POST['key'], $_POST['value']);
						} else {
							http_response_code(400);
							exit('There was a problem with the request.');
						}
						break;


					case 'unset':
						if (isset($_POST['key'])) {
							GlobalSettings::unset($_POST['key']);
						} else {
							http_response_code(400);
							exit('There was a problem with the request.');
						}
						break;


					default:
						http_response_code(400);
						exit('There was a problem with the request.');
						break;
				}
				break;


			default:
				http_response_code(400);
				exit('There was a problem with the request.');
				break;
		}
	} else {
		http_response_code(400);
		exit('There was a problem with the request.');
	}
} else {
	http_response_code(403);
	exit('There was a problem with the request.');
}
