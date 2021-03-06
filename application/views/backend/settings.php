<script type="text/javascript"
        src="<?php echo $base_url; ?>/assets/js/backend_settings.js"></script>
<script type="text/javascript"
        src="<?php echo $base_url; ?>/assets/js/working_plan.js"></script>
<script type="text/javascript"
        src="<?php echo $base_url; ?>/assets/ext/jquery-ui/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript"
        src="<?php echo $base_url; ?>/assets/ext/jquery-jeditable/jquery.jeditable.min.js"></script>

<script type="text/javascript">
    var GlobalVariables = {
        'csrfToken'     : <?php echo json_encode($this->security->get_csrf_hash()); ?>,
        'baseUrl'       : <?php echo '"' . $base_url . '"'; ?>,
        'dateFormat'    : <?php echo json_encode($date_format); ?>,
        'userSlug'      : <?php echo '"' . $role_slug . '"'; ?>,
        'book_advance_timeout' : <?php echo $book_advance_timeout; ?>,
        'settings'      : {
            'system'    : <?php echo json_encode($system_settings); ?>,
            'user'      : <?php echo json_encode($user_settings); ?>
        },
        'user'          : {
            'id'        : <?php echo $user_id; ?>,
            'email'     : <?php echo '"' . $user_email . '"'; ?>,
            'role_slug' : <?php echo '"' . $role_slug . '"'; ?>,
            'privileges': <?php echo json_encode($privileges); ?>
        }
    };

    $(document).ready(function() {
        BackendSettings.initialize(true);
    });
</script>

<div id="settings-page" class="row-fluid">
    <ul class="nav nav-tabs">
        <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['view'] == TRUE) { ?>
        <li role="representation" class="general-tab tab"><a><?php echo $this->lang->line('general'); ?></a></li>
        <?php } ?>

        <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['view'] == TRUE) { ?>
        <li role="representation" class="business-logic-tab tab"><a><?php echo $this->lang->line('appointments'); ?></a></li>
        <?php } ?>

        <?php if ($privileges[PRIV_USER_SETTINGS]['view'] == TRUE) { ?>
        <li role="representation" class="user-tab tab"><a><?php echo $this->lang->line('current_user'); ?></a></li>
        <?php } ?>

    </ul>

    <?php
        // --------------------------------------------------------------
        //
        // GENERAL TAB
        //
        // --------------------------------------------------------------
    ?>
    <?php $hidden = ($privileges[PRIV_SYSTEM_SETTINGS]['view'] == TRUE) ? '' : 'hidden'; ?>
    <div id="general" class="tab-content <?php echo $hidden; ?>">
        <form>
            <fieldset>
                <legend>
                    <?php echo $this->lang->line('general_settings'); ?>
                    <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['edit'] == TRUE) { ?>
                    <button type="button" class="save-settings btn btn-primary btn-xs"
                            title="<?php echo $this->lang->line('save'); ?>">
                        <span class="glyphicon glyphicon-floppy-disk"></span>
                    </button>
                    <?php } ?>
                </legend>

                <div class="wrapper row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="company-name"><?php echo $this->lang->line('company_name'); ?> *</label>
                            <input type="text" id="company-name" data-field="company_name" class="required form-control">
                            <span class="help-block">
                                <?php echo $this->lang->line('company_name_hint'); ?>
                            </span>
                        </div>

                        <div class="form-group">
                            <label for="company-email"><?php echo $this->lang->line('company_email'); ?> *</label>
                            <input type="text" id="company-email" data-field="company_email" class="required form-control">
                            <span class="help-block">
                                <?php echo $this->lang->line('company_email_hint'); ?>
                            </span>
                        </div>

                        <div class="form-group">
                            <label for="company-link"><?php echo $this->lang->line('company_link'); ?> *</label>
                            <input type="text" id="company-link" data-field="company_link" class="required form-control">
                            <span class="help-block">
                                <?php echo $this->lang->line('company_link_hint'); ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!--<div class="form-group">-->
                        <!--    <label for="google-analytics-code">-->
                        <!--        Google Analytics ID</label>-->
                        <!--    <input type="text" id="google-analytics-code" placeholder="UA-XXXXXXXX-X"-->
                        <!--        data-field="google_analytics_code" class="form-control">-->
                        <!--    <span class="help-block">-->
                        <!--        <?php echo $this->lang->line('google_analytics_code_hint'); ?>-->
                        <!--    </span>-->
                        <!--</div>-->
                        <!--<div class="form-group">-->
                        <!--    <label for="date-format">-->
                        <!--        <?php echo $this->lang->line('date_format'); ?>-->
                        <!--    </label>-->
                        <!--    <select class="form-control" id="date-format" data-field="date_format">-->
                        <!--        <option value="DMY">DMY</option>-->
                        <!--        <option value="MDY">MDY</option>-->
                        <!--        <option value="YMD">YMD</option>-->
                        <!--    </select>-->
                        <!--    <span class="help-block">-->
                        <!--        <?php echo $this->lang->line('date_format_hint'); ?>-->
                        <!--    </span>-->
                        <!--</div>-->
                        <div class="form-group">
                            <label><?php echo $this->lang->line('customer_notifications'); ?></label>
                            <br>
                            <button type="button" id="customer-notifications" class="btn btn-default" data-toggle="button" aria-pressed="false">
                                <span class="glyphicon glyphicon-envelope"></span>
                                <?php echo $this->lang->line('receive_notifications'); ?>
                            </button>
                            <span class="help-block">
                                <?php echo $this->lang->line('customer_notifications_hint'); ?>
                            </span>
                        </div>
                        <!--<div class="form-group">-->
                        <!--    <label for="require-captcha">-->
                        <!--        CAPTCHA-->
                        <!--    </label>-->
                        <!--    <br>-->
                        <!--    <button type="button" id="require-captcha" class="btn btn-default" data-toggle="button" aria-pressed="false">-->
                        <!--        <span class="glyphicon glyphicon-lock"></span>-->
                        <!--        <?php echo $this->lang->line('require_captcha'); ?>-->
                        <!--    </button>-->
                        <!--    <span class="help-block">-->
                        <!--        <?php echo $this->lang->line('require_captcha_hint'); ?>-->
                        <!--    </span>-->
                        <!--</div>-->
                    </div>
                </div>
            </fieldset>
        </form>
    </div>

    <?php
        // --------------------------------------------------------------
        //
        // BUSINESS LOGIC TAB
        //
        // --------------------------------------------------------------
    ?>
    <?php $hidden = ($privileges[PRIV_SYSTEM_SETTINGS]['view'] == TRUE) ? '' : 'hidden'; ?>
    <div id="business-logic" class="tab-content <?php echo $hidden; ?>">
        <form>
            <fieldset>
                <legend>
                    <?php echo $this->lang->line('<business_lo></business_lo>gic'); ?>
                    <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['edit'] == TRUE) { ?>
                    <button type="button" class="save-settings btn btn-primary btn-xs"
                            title="<?php echo $this->lang->line('save'); ?>">
                        <span class="glyphicon glyphicon-floppy-disk"></span>
                    </button>
                    <?php } ?>
                </legend>

                <div class="row-fluid">
                    <div class="col-md-5 breaks-wrapper">
                        <h4><?php echo $this->lang->line('appointments'); ?></h4>

                        <span class="help-block">
                            <?php echo $this->lang->line('edit_appointments_hint'); ?>
                        </span>
                        <br>
                        <table class="table table-striped">
                        	<thead>
                        		<tr>
                        			<th> <?php echo $this->lang->line('settings'); ?></th>
                        		</tr>
                        	</thead>
                             <tr>
                               	 <th><label for="missed-app-num"><?php echo $this->lang->line('missed_appointments_limit'); ?></label></th>
                                 <td><input type="text" id="missed-app-num" data-field="missed_app_num" /></td>
                             </tr>
                             <tr>
                               	 <th><label for="missed-app-timeframe"><?php echo $this->lang->line('missed_app_timeframe'); ?></label></th>
                                 <td><input type="text" id="missed-app-timeframe" data-field="missed_app_timeframe" /></td>
                             </tr>
                             <tr>
                               	 <th><label for="app-probation"><?php echo $this->lang->line('app_probation'); ?></label></th>
                                 <td><input type="text" id="app-probation" data-field="app_probation" /></td>
                             </tr>
                             <tr>
                               	 <th><label for="book-advance-timeout"><?php echo $this->lang->line('appointment_length'); ?></label></th>
                                 <td><input type="text" id="book-advance-timeout" data-field="book_advance_timeout" /></td>
                             </tr>                        </table>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>

    <?php
        // --------------------------------------------------------------
        //
        // USER TAB
        //
        // --------------------------------------------------------------
    ?>
    <?php $hidden = ($privileges[PRIV_USER_SETTINGS]['view'] == TRUE) ? '' : 'hidden'; ?>
    <div id="user" class="tab-content <?php echo $hidden; ?>">
        <form>
            <fieldset class="col-md-5 personal-info-wrapper">
                <legend>
                    <?php echo $this->lang->line('personal_information'); ?>
                    <?php if ($privileges[PRIV_USER_SETTINGS]['edit'] == TRUE) { ?>
                    <button type="button" class="save-settings btn btn-primary btn-xs"
                            title="<?php echo $this->lang->line('save'); ?>">
                        <span class="glyphicon glyphicon-floppy-disk"></span>
                    </button>
                    <?php } ?>
                </legend>

                <input type="hidden" id="user-id" />

                <div class="form-group">
                    <label for="first-name"><?php echo $this->lang->line('first_name'); ?> *</label>
                    <input type="text" id="first-name" class="form-control required" />
                </div>

                <div class="form-group">
                    <label for="last-name"><?php echo $this->lang->line('last_name'); ?> *</label>
                    <input type="text" id="last-name" class="form-control required" />
                </div>

                <div class="form-group">
                    <label for="email"><?php echo $this->lang->line('email'); ?> *</label>
                    <input type="text" id="email" class="form-control required" />
                </div>

                <div class="form-group">
                    <label for="phone-number"><?php echo $this->lang->line('phone_number'); ?> *</label>
                    <input type="text" id="phone-number" class="form-control required" />
                </div>

                <div class="form-group">
                    <label for="mobile-number"><?php echo $this->lang->line('mobile_number'); ?></label>
                    <input type="text" id="mobile-number" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="address"><?php echo $this->lang->line('address'); ?></label>
                    <input type="text" id="address" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="city"><?php echo $this->lang->line('city'); ?></label>
                    <input type="text" id="city" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="state"><?php echo $this->lang->line('state'); ?></label>
                    <input type="text" id="state" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="zip-code"><?php echo $this->lang->line('zip_code'); ?></label>
                    <input type="text" id="zip-code" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="notes"><?php echo $this->lang->line('notes'); ?></label>
                    <textarea id="notes" class="form-control" rows="3"></textarea>
                </div>
            </fieldset>

            <fieldset class="col-md-5 miscellaneous-wrapper">
                <legend><?php echo $this->lang->line('system_login'); ?></legend>

                <div class="form-group">
                    <label for="username"><?php echo $this->lang->line('username'); ?> *</label>
                    <input type="text" id="username" class="form-control required" />
                </div>

                <div class="form-group">
                    <label for="password"><?php echo $this->lang->line('password'); ?></label>
                    <input type="password" id="password" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="retype-password"><?php echo $this->lang->line('retype_password'); ?></label>
                    <input type="password" id="retype-password" class="form-control" />
                </div>

                <button type="button" id="user-notifications" class="btn btn-default" data-toggle="button">
                    <span class="glyphicon glyphicon-envelope"></span>
                    <?php echo $this->lang->line('receive_notifications'); ?>
                </button>
            </fieldset>
        </form>
    </div>


 <!------------------------------------------------------------------>

 <!--ABOUT TAB-->

 <!------------------------------------------------------------------>
  <!--  <div id="about" class="tab-content">-->
  <!--      <h3>Easy!Appointments</h3>-->
  <!--      <p>-->
  <!--          <?php echo $this->lang->line('about_ea_info'); ?>-->
  <!--      </p>-->

  <!--      <br>-->

  <!--      <div class="current-version">-->
  <!--          <?php-->
  <!--              echo $this->lang->line('current_version') . ' ';-->
  <!--              echo $this->config->item('ea_version');-->
  <!--              $release_title = $this->config->item('ea_release_title');-->
  <!--              if ($release_title != '') {-->
  <!--                  echo ' - ' . $release_title;-->
  <!--              }-->
  <!--          ?>-->
  <!--      </div>-->

		<!--<br>-->

  <!--      <h3><?php echo $this->lang->line('support'); ?></h3>-->
  <!--      <p>-->
  <!--          <?php echo $this->lang->line('about_ea_support'); ?>-->
  <!--          <br><br>-->
  <!--          <a href="http://easyappointments.org">-->
  <!--              <?php echo $this->lang->line('official_website'); ?>-->
  <!--          </a>-->
  <!--          |-->
  <!--          <a href="https://groups.google.com/forum/#!forum/easy-appointments">-->
  <!--              <?php echo $this->lang->line('support_group'); ?>-->
  <!--          </a>-->
  <!--          |-->
  <!--          <a href="https://github.com/alextselegidis/easyappointments/issues">-->
  <!--              <?php echo $this->lang->line('project_issues'); ?>-->
  <!--          </a>-->
  <!--          |-->
  <!--          <a href="http://easyappointments.wordpress.com">-->
  <!--              E!A Blog-->
  <!--          </a>-->
  <!--          |-->
  <!--          <a href="https://plus.google.com/communities/105333709485142846840">-->
  <!--              <?php echo $this->lang->line('google_plus_community'); ?>-->
  <!--          </a>-->
  <!--      </p>-->

		<!--<br>-->

		<!--<h3><?php echo $this->lang->line('license'); ?></h3>-->
		<!--<p>-->
  <!--          <?php echo $this->lang->line('about_ea_license'); ?>-->
  <!--          <a href="http://www.gnu.org/copyleft/gpl.html">http://www.gnu.org/copyleft/gpl.html</a>-->
  <!--      </p>-->
  <!--  </div>-->
</div>
