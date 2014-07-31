<?php
// This file is part of the custom Moodle tsc_2013 theme
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Renderers to align Moodle's HTML with that expected by tsc_2013
 *
 * @package    theme_tsc_2013
 * @copyright  2014 Julian Ridden http://moodleman.net
 * @authors    Julian Ridden -  Bootstrap 3 work by Bas Brands, David Scotson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class theme_tsc_2013_core_renderer extends core_renderer {

	protected $enrolledcourses = null;

	public function edit_button(moodle_url $url) {
        $url->param('sesskey', sesskey());    
        if ($this->page->user_is_editing()) {
            $url->param('edit', 'off');
            $btn = 'btn-danger';
            $title = get_string('turneditingoff');
            $icon = 'fa-power-off';
        } else {
            $url->param('edit', 'on');
            $btn = 'btn-success';
            $title = get_string('turneditingon');
            $icon = 'fa-edit';
        }
        return html_writer::tag('a', html_writer::start_tag('i', array('class' => $icon.' fa fa-fw')).
               html_writer::end_tag('i'), array('href' => $url, 'class' => 'btn '.$btn, 'title' => $title));
    }

    protected function mycourses($CFG,$sidebar){
        $mycourses = enrol_get_users_courses($_SESSION['USER']->id);

        $courselist = array();
        foreach ($mycourses as $key=>$val){
            $courselist[] = $val->id;
        }

        $content = '';

        for($x=1;$x<=sizeof($courselist);$x++){
            $course = get_course($courselist[$x-1]);
            $title = $course->fullname;

            if ($course instanceof stdClass) {
                require_once($CFG->libdir. '/coursecatlib.php');
                $course = new course_in_list($course);
            }

            $url = $CFG->wwwroot."/theme/tsc_2013/pix/coursenoimage.jpg";
            foreach ($course->get_course_overviewfiles() as $file) {
                $isimage = $file->is_valid_image();
                $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                        '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                        $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                if (!$isimage) {
                    $url = $CFG->wwwroot."/theme/tsc_2013/pix/coursenoimage.jpg";
                }
            }

            $content .= '<div class="view view-second view-mycourse '.(($x%3==0)?'view-nomargin':'').'">
                            <img src="'.$url.'" />
                            <div class="mask">
                                <h2>'.$title.'</h2>
                                <a href="'.$CFG->wwwroot.'/course/view.php?id='.$courselist[$x-1].'" class="info">Enter</a>
                            </div>
                        </div>';
        }
        return $content;
    }

    /*
     * This renders a notification message.
     * Uses bootstrap compatible html.
     */
    public function notification($message, $classes = 'notifyproblem') {
        $message = clean_text($message);

        if ($classes == 'notifyproblem') {
            return html_writer::div($message, 'alert alert-danger');
        }
        if ($classes == 'notifywarning') {
            return html_writer::div($message, 'alert alert-warning');
        }
        if ($classes == 'notifysuccess') {
            return html_writer::div($message, 'alert alert-success');
        }
        if ($classes == 'notifymessage') {
            return html_writer::div($message, 'alert alert-info');
        }
        if ($classes == 'redirectmessage') {
            return html_writer::div($message, 'alert alert-block alert-info');
        }
        if ($classes == 'notifytiny') {
            // Not an appropriate semantic alert class!
            return $this->debug_listing($message);
        }
        return html_writer::div($message, $classes);
    }

    private function debug_listing($message) {
        $message = str_replace('<ul style', '<ul class="list-unstyled" style', $message);
        return html_writer::tag('pre', $message, array('class' => 'alert alert-info'));
    }

    public function navbar() {
        $breadcrumbs = '';
        foreach ($this->page->navbar->get_items() as $item) {
            $item->hideicon = true;
            $breadcrumbs .= '<li>'.$this->render($item).'</li>';
        }
        return "<ol class=breadcrumb>$breadcrumbs</ol>";
    }

    public function custom_menu($custommenuitems = '') {
    // The custom menu is always shown, even if no menu items
    // are configured in the global theme settings page.
        global $CFG;

        if (!empty($CFG->custommenuitems)) {
            $custommenuitems .= $CFG->custommenuitems;
        }
        $custommenu = new custom_menu($custommenuitems, current_language());
        return $this->render_custom_menu($custommenu);
    }

    protected function render_custom_menu(custom_menu $menu) {
        global $CFG, $USER;

        // TODO: eliminate this duplicated logic, it belongs in core, not
        // here. See MDL-39565.

        $content = '<ul class="nav navbar-nav">';
        foreach ($menu->get_children() as $item) {
            $content .= $this->render_custom_menu_item($item, 1);
        }

        return $content.'</ul>';
    }

	public function admin_menu() {
		$adminmenu = new custom_menu('',current_language());
		return $this->render_admin_menu($adminmenu);
	}


	    public function search_form(moodle_url $formtarget, $searchvalue) {
        $content = html_writer::start_tag('form', array('class' => 'adminsearchform', 'method' => 'get', 'action' => $formtarget, 'role' => 'search', ));
        $content .= html_writer::start_div('input-group', array('style' =>'width:95%; margin: 0 auto;'));
        $content .= html_writer::empty_tag('input', array('id' => 'adminsearchquery', 'type' => 'text', 'name' => 'query', 'class' => 'form-control', 'placeholder' => s(get_string('searchinsettings', 'admin')), 'value' => s($searchvalue),));
        $content .= html_writer::start_span('input-group-btn');
        $content .= html_writer::empty_tag('input', array('type' => 'submit', 'class' => 'btn btn-default', 'value'=>s(get_string('go'))));
        $content .= html_writer::end_span();
        $content .= html_writer::end_div();
        $content .= html_writer::end_tag('form');
        return $content;
    }

public function settings_tree(settings_navigation $navigation) {
       
        $content = $this->navigation_node($navigation, array('class'=>'nav navbar-nav'));
        return $content;
    }

 protected function navigation_node(navigation_node $node, $attrs=array('class'=>'dropdown-menu')) {
        global $CFG, $PAGE;
        $items = $node->children;

        // exit if empty, we don't want an empty ul element
        if ($items->count()==0) {
            return '';
        }

        // array of nested li elements
        $lis = array();
        foreach ($items as $item) {
            if (!$item->display) {
                continue;
            }

            // Skip pointless "Current course" node, go straight to its last (sole) child
            if ($item->key === 'usercurrentsettings') {
                $item = $item->children->last();
            }

            $isbranch = ($item->children->count()>0  || $item->nodetype==navigation_node::NODETYPE_BRANCH);
            $hasicon = (!$isbranch && $item->icon instanceof renderable);

            if ($isbranch) {
                $item->hideicon = true;
            }
            
            //$content = '';
            $content = $this->render($item);

            // this applies to the li item which contains all child lists too
            $liclasses = array($item->get_css_type());
            $liexpandable = array();
            if (!$item->forceopen || (!$item->forceopen && $item->collapse) || ($item->children->count()==0  && $item->nodetype==navigation_node::NODETYPE_BRANCH)) {
                //$liclasses[] = 'collapsed ';
            }
            if ($isbranch) {
                $liclasses[] = 'dropdown';
                //$liclasses[] = 'dropdown contains_branch';
                
                $liexpandable = array('aria-expanded' => in_array('collapsed', $liclasses) ? "false" : "true");
            } else if ($hasicon) {
                $liclasses[] = 'item_with_icon';
            }
            
            $liattr = array('class' => join(' ',$liclasses)) + $liexpandable;
            // class attribute on the div item which only contains the item content
            $divclasses = array();
            //$divclasses = array('tree_item');
            $datatoggle = array();
            if ($isbranch) {
                $divclasses[] = 'dropdown-toggle';
                $datatoggle[] = 'dropdown';
                //$divclasses[] = 'branch dropdown-toggle';
                
            } else {
                $divclasses[] = 'leaf';
            }
            if (!empty($item->classes) && count($item->classes)>0) {
                $divclasses[] = join(' ', $item->classes);
            }
           if(!empty($datatoggle)) {
            	$divattr = array('class'=>join(' ', $divclasses), 'data-toggle'=>join(' ', $datatoggle));
            } else {
	            $divattr = array('class'=>join(' ', $divclasses));
            }
            if (!empty($item->id)) {
                $divattr['id'] = $item->id;
            }
            
           $content = html_writer::tag('a', $content, $divattr) . $this->navigation_node($item);
            
            $content = html_writer::tag('li', $content, $liattr);
            $lis[] = $content;
        }

        if (count($lis)) {
            return html_writer::tag('ul', implode("\n", $lis), $attrs);
        } else {
            return '';
        }
    }

	
	protected function render_admin_menu(custom_menu $menu) {
		global $CFG, $PAGE;
		$url = $CFG->wwwroot . '/admin/search.php';
		
		$bob = new stdClass();
        $bob = $this->settings_tree($this->page->settingsnav);
        //var_dump($bob);
		$content ='';
		//$content ='<div>';
		$content .= $bob;
        
        return $content;
	}

    public function user_menu() {
        global $CFG;
        $usermenu = new custom_menu('', current_language());
        return $this->render_user_menu($usermenu);
    }

    protected function render_user_menu(custom_menu $menu) {
        global $CFG, $USER, $DB, $PAGE; //tsc_2013 add $PAGE

        $addusermenu = true;
        $addlangmenu = true;
        $addmessagemenu = true;

        if (!isloggedin() || isguestuser()) {
            $addmessagemenu = false;
        }

        if ($addmessagemenu) {
            $messages = $this->get_user_messages();
            $messagecount = count($messages);
            //tsc_2013 custom line start
            $messagemenu = $menu->add('<i class="fa fa-envelope"></i>'.
            //tsc_2013 custom line end
                $messagecount . ' ' . get_string('messages', 'message'),
                new moodle_url('/message/index.php', array('viewing' => 'recentconversations')),
                get_string('messages', 'message'),
                9999
            );
            foreach ($messages as $message) {

                if (!$message->from) { // Workaround for issue #103.
                    continue;
                }
                $senderpicture = new user_picture($message->from);
                $senderpicture->link = false;
                $senderpicture = $this->render($senderpicture);

                $messagecontent = $senderpicture;
                $messagecontent .= html_writer::start_span('msg-body');
                $messagecontent .= html_writer::start_span('msg-title');
                $messagecontent .= html_writer::span($message->from->firstname . ': ', 'msg-sender');
                $messagecontent .= $message->text;
                $messagecontent .= html_writer::end_span();
                $messagecontent .= html_writer::start_span('msg-time');
                $messagecontent .= html_writer::tag('i', '', array('class' => 'icon-time'));
                $messagecontent .= html_writer::span($message->date);
                $messagecontent .= html_writer::end_span();

                $messageurl = new moodle_url('/message/index.php', array('user1' => $USER->id, 'user2' => $message->from->id));
                $messagemenu->add($messagecontent, $messageurl, $message->state);
            }
        }
        
        //KRB HERE
        
        $displaymycourses = (empty($this->page->theme->settings->displaymycoursesmenu)) ? false : $this->page->theme->settings->displaymycoursesmenu;
        if (isloggedin() && !isguestuser() && $displaymycourses) {
            switch ($displaymycourses) {
                case 1:
                    $branchtitle = get_string('myclasses', 'theme_tsc_2013');
                    break;
                case 2: 
                    $branchtitle = get_string('mycourses', 'theme_tsc_2013');
                    break;
                case 3:
                    $branchtitle = get_string('mymodules', 'theme_tsc_2013');
                    break;
                case 4:
                    $branchtitle = get_string('mysubjects', 'theme_tsc_2013');
                    break;
                case 5:
                    $branchtitle = get_string('myunits', 'theme_tsc_2013');
                    break;
                default:
                    $branchtitle = get_string('mycourses', 'theme_tsc_2013');
            }
			
			$branchlabel = html_writer::tag('i', '', array('class' => 'fa fa-dashboard'));
			$branchlabel .= html_writer::tag('span', ' '.$branchtitle);
            $branchurl   = new moodle_url('/my/index.php');
            $branchsort  = 10000;
 
            $mycoursesmenu = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);

            /*
            $hometext = get_string('myhome');
            $homelabel = html_writer::tag('i', '', array('class' => 'fa fa-home'));
            $homelabel .= html_writer::tag('span', ' '.$hometext);
            $mycoursesmenu->add($homelabel, new moodle_url('/my/index.php'), $hometext);
			*/
			
            $courseicons = array('list', 'list-alt', 'book', 'tasks', 'suitcase');
            
            $courses = $this->get_enrolled_courses();
            $rhosts = array();
            $rcourses = array();
            if (!empty($CFG->mnet_dispatcher_mode) && $CFG->mnet_dispatcher_mode==='strict') {
                $rcourses = get_my_remotecourses($USER->id);
                $rhosts   = get_my_remotehosts();
            }
            if (!empty($courses) || !empty($rcourses) || !empty($rhosts)) {
                foreach ($courses as $course) {
                    if ($course->visible){
                        $coursetext = format_string($course->fullname);
                        $courselabel = html_writer::tag('i', '', array('class' => 'fa fa-'.$courseicons[$course->id % 5])); // 5 is the courseicons array length.
                        $courselabel .= html_writer::tag('span', ' '.$coursetext);

                        $mycoursesmenu->add($courselabel, new moodle_url('/course/view.php?id='.$course->id), format_string($course->shortname));
                    }
                }
                // MNET
                if (!empty($rcourses)) {
                    // at the IDP, we know of all the remote courses
                    foreach ($rcourses as $course) {
                        $url = new moodle_url('/auth/mnet/jump.php', array(
                            'hostid' => $course->hostid,
                            'wantsurl' => '/course/view.php?id='. $course->remoteid
                        ));
                        $tooltip = format_string($course->hostname).' : '.format_string($course->cat_name).' : '.format_string($course->shortname);

                        $coursetext = format_string($course->fullname);
                        if ($this->page->theme->settings->fontawesome) {
                            $courselabel = html_writer::tag('i', '', array('class' => 'fa fa-'.$courseicons[$course->remoteid % 5])); // 5 is the courseicons array length.
                        } else {
                            $courselabel = html_writer::tag('span', '', array('class' => 'glyphicon glyphicon-'.$courseicons[$course->remoteid % 5]));
                        }
                        $courselabel .= html_writer::tag('span', ' '.$coursetext);

                        $mycoursesmenu->add($courselabel, $url, $tooltip);
                    }
                }
                if (!empty($rhosts)) {
                    // non-IDP, we know of all the remote servers, but not courses
                    foreach ($rhosts as $host) {
                        $coursetext = format_string($course->fullname);
                        if ($this->page->theme->settings->fontawesome) {
                            $courselabel = html_writer::tag('i', '', array('class' => 'fa fa-'.$courseicons[0]));
                        } else {
                            $courselabel = html_writer::tag('span', '', array('class' => 'glyphicon glyphicon-'.$courseicons[0]));
                        }
                        $courselabel .= html_writer::tag('span', ' '.$coursetext);

                        $mycoursesmenu->add($courselabel, html_writer::link($host['url'], s($host['name']), array('title' => s($host['name']))), $host['count'] . ' ' . get_string('courses'));
                    }
                }
             } else {
                $noenrolments = get_string('noenrolments', 'theme_tsc_2013');
                $mycoursesmenu->add('<em>'.$noenrolments.'</em>', new moodle_url('/'), $noenrolments);
             }
        }
        
        // END KRB 

        $langs = get_string_manager()->get_list_of_translations();
        if (count($langs) < 2
        or empty($CFG->langmenu)
        or ($this->page->course != SITEID and !empty($this->page->course->lang))) {
            $addlangmenu = false;
        }

        if ($addlangmenu) {
            $language = $menu->add(get_string('language'), new moodle_url('#'), get_string('language'), 10000);
            foreach ($langs as $langtype => $langname) {
                $language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }

        if ($addusermenu) {
            if (isloggedin() && !isguestuser()) {
                $usermenu = $menu->add('<i class="fa fa-user"></i>' .fullname($USER), new moodle_url('#'), fullname($USER), 10001);

                if (!empty($PAGE->theme->settings->enablemy)) {
                    $usermenu->add(
                        '<i class="fa fa-briefcase"></i>' . get_string('mydashboard','theme_tsc_2013'),
                        new moodle_url('/my', array('id'=>$USER->id)),
                        get_string('mydashboard','theme_tsc_2013')
                    );
                }

                if (!empty($PAGE->theme->settings->enableprofile)) {
                    $usermenu->add(
                        '<i class="fa fa-user"></i>' . get_string('viewprofile'),
                        new moodle_url('/user/profile.php', array('id' => $USER->id)),
                        get_string('viewprofile')
                    );
                }

                if (!empty($PAGE->theme->settings->enableeditprofile)) {
                        $usermenu->add(
                        '<i class="fa fa-cog"></i>' . get_string('editmyprofile'),
                        new moodle_url('/user/edit.php', array('id' => $USER->id)),
                    get_string('editmyprofile')
                    );
                }

                if (!empty($PAGE->theme->settings->enableprivatefiles)) {
                    $usermenu->add(
                        '<i class="fa fa-file"></i>' . get_string('privatefiles', 'block_private_files'),
                        new moodle_url('/user/files.php', array('id' => $USER->id)),
                        get_string('privatefiles', 'block_private_files')
                    );
                }

                if (!empty($PAGE->theme->settings->enablebadges)) {
                    $usermenu->add(
                        '<i class="fa fa-certificate"></i>' . get_string('badges'),
                        new moodle_url('/badges/mybadges.php', array('id' => $USER->id)),
                        get_string('badges')
                    );
                }

                if (!empty($PAGE->theme->settings->enablecalendar)) {
                    $usermenu->add(
                        '<i class="fa fa-calendar"></i>' . get_string('pluginname', 'block_calendar_month'),
                        new moodle_url('/calendar/view.php', array('id' => $USER->id)),
                        get_string('pluginname', 'block_calendar_month')
                    );
                }

                // Add custom links to menu
                $customlinksnum = (empty($PAGE->theme->settings->usermenulinks)) ? false : $PAGE->theme->settings->usermenulinks;
                if ($customlinksnum !=0) {
                    foreach (range(1, $customlinksnum) as $customlinksnumber) {
                        $cli = "customlinkicon$customlinksnumber";
                        $cln = "customlinkname$customlinksnumber";
                        $clu = "customlinkurl$customlinksnumber";

                        if (!empty($PAGE->theme->settings->enablecalendar)) {
                            $usermenu->add(
                                '<i class="fa fa-'.$PAGE->theme->settings->$cli.'"></i>' .$PAGE->theme->settings->$cln,
                                new moodle_url($PAGE->theme->settings->$clu, array('id' => $USER->id)),
                                $PAGE->theme->settings->$cln
                            );
                        }
                    }
                }

                $usermenu->add(
                    '<i class="fa fa-lock"></i>' . get_string('logout'),
                    new moodle_url('/login/logout.php', array('sesskey' => sesskey(), 'alt' => 'logout')),
                    get_string('logout')
                );
            } else {
                $usermenu = $menu->add('<i class="fa fa-key"></i>' .get_string('login'), new moodle_url('/login/index.php'), get_string('login'), 10001);
            }
        }

        $content = '<ul class="nav navbar-nav navbar-right">';
        foreach ($menu->get_children() as $item) {
          $content .= $this->render_custom_menu_item($item, 1);
        }
//		$content .= '<li><a href="#" title="Bob"><i class="fa fa-lock"></i>Bob</a></li>';   // TODO: This is the format for adding menus to the User Menu KRB
        return $content.'</ul>';
    }

   protected function process_user_messages() {

        $messagelist = array();

        foreach ($usermessages as $message) {
            $cleanmsg = new stdClass();
            $cleanmsg->from = fullname($message);
            $cleanmsg->msguserid = $message->id;

            $userpicture = new user_picture($message);
            $userpicture->link = false;
            $picture = $this->render($userpicture);

            $cleanmsg->text = $picture . ' ' . $cleanmsg->text;

            $messagelist[] = $cleanmsg;
        }

        return $messagelist;
    }
    
    protected function get_enrolled_courses() {
        if ($this->enrolledcourses == null) {
            global $CFG;
            // Info from: /course/renderer.php::frontpage_my_courses().
            if (!empty($CFG->navsortmycoursessort)) {
                // sort courses the same as in navigation menu
                $sortorder = 'visible DESC,'. $CFG->navsortmycoursessort.' ASC';
            } else {
                $sortorder = 'visible DESC,sortorder ASC';
            }
            $this->enrolledcourses = enrol_get_my_courses('summary, summaryformat', $sortorder);
        }
        return $this->enrolledcourses;
    }


   protected function get_user_messages() {
        global $USER, $DB;
        $messagelist = array();
        $maxmessages = 5;

        $readmessagesql = "SELECT id, smallmessage, useridfrom, useridto, timecreated, fullmessageformat, notification
        				     FROM {message_read}
        			        WHERE useridto = :userid
        			     ORDER BY timecreated DESC
        			        LIMIT $maxmessages";
        $newmessagesql = "SELECT id, smallmessage, useridfrom, useridto, timecreated, fullmessageformat, notification
        					FROM {message}
        			       WHERE useridto = :userid";

        $readmessages = $DB->get_records_sql($readmessagesql, array('userid' => $USER->id));

        $newmessages = $DB->get_records_sql($newmessagesql, array('userid' => $USER->id));

        foreach ($newmessages as $message) {
            $messagelist[] = $this->bootstrap_process_message($message, 'new');
        }

        foreach ($readmessages as $message) {
            $messagelist[] = $this->bootstrap_process_message($message, 'old');
        }
        return $messagelist;

    }

   protected function bootstrap_process_message($message, $state) {
        global $DB;
        $messagecontent = new stdClass();

        if ($message->notification) {
            $messagecontent->text = get_string('unreadnewnotification', 'message');
        } else {
            if ($message->fullmessageformat == FORMAT_HTML) {
                $message->smallmessage = html_to_text($message->smallmessage);
            }
            if (core_text::strlen($message->smallmessage) > 15) {
                $messagecontent->text = core_text::substr($message->smallmessage, 0, 15).'...';
            } else {
                $messagecontent->text = $message->smallmessage;
            }
        }

        if ((time() - $message->timecreated ) <= (3600 * 3)) {
            $messagecontent->date = format_time(time() - $message->timecreated);
        } else {
            $messagecontent->date = userdate($message->timecreated, get_string('strftimetime', 'langconfig'));
        }

        $messagecontent->from = $DB->get_record('user', array('id' => $message->useridfrom));
        $messagecontent->state = $state;
        return $messagecontent;
    }

   protected function render_custom_menu_item(custom_menu_item $menunode, $level = 0 ) {
        static $submenucount = 0;

        if ($menunode->has_children()) {

            if ($level == 1) {
                $dropdowntype = 'dropdown';
            } else {
                $dropdowntype = 'dropdown-submenu';
            }

            $content = html_writer::start_tag('li', array('class' => $dropdowntype));
            // If the child has menus render it as a sub menu.
            $submenucount++;
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#cm_submenu_'.$submenucount;
            }
            $linkattributes = array(
                'href' => $url,
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown',
                'title' => $menunode->get_title(),
            );
            $content .= html_writer::start_tag('a', $linkattributes);
            $content .= $menunode->get_text();
            if ($level == 1) {
                $content .= '<b class="caret"></b>';
            }
            $content .= '</a>';
            $content .= '<ul class="dropdown-menu">';
            foreach ($menunode->get_children() as $menunode) {
                $content .= $this->render_custom_menu_item($menunode, 0);
            }
            $content .= '</ul>';
        } else {
            $content = '<li>';
            // The node doesn't have children so produce a final menuitem.
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#';
            }
            $content .= html_writer::link($url, $menunode->get_text(), array('title' => $menunode->get_title()));
        }
        return $content;
    }

   protected function render_tabtree(tabtree $tabtree) {
        if (empty($tabtree->subtree)) {
            return '';
        }
        $firstrow = $secondrow = '';
        foreach ($tabtree->subtree as $tab) {
            $firstrow .= $this->render($tab);
            if (($tab->selected || $tab->activated) && !empty($tab->subtree) && $tab->subtree !== array()) {
                $secondrow = $this->tabtree($tab->subtree);
            }
        }
        return html_writer::tag('ul', $firstrow, array('class' => 'nav nav-tabs nav-justified')) . $secondrow;
    }

   protected function render_tabobject(tabobject $tab) {
        if ($tab->selected or $tab->activated) {
            return html_writer::tag('li', html_writer::tag('a', $tab->text), array('class' => 'active'));
        } else if ($tab->inactive) {
            return html_writer::tag('li', html_writer::tag('a', $tab->text), array('class' => 'disabled'));
        } else {
            if (!($tab->link instanceof moodle_url)) {
                // Backward compatibility when link was passed as quoted string.
                $link = "<a href=\"$tab->link\" title=\"$tab->title\">$tab->text</a>";
            } else {
                $link = html_writer::link($tab->link, $tab->text, array('title' => $tab->title));
            }
            return html_writer::tag('li', $link);
        }
    }

    protected function render_pix_icon(pix_icon $icon) {
        if ($this->page->theme->settings->fonticons === '1'
            && $icon->attributes["alt"] === ''
            && $this->replace_moodle_icon($icon->pix) !== false) {
            return $this->replace_moodle_icon($icon->pix);
        }
        return parent::render_pix_icon($icon);
    }


    protected function replace_moodle_icon($name) {
        $icons = array(
            'add' => 'plus',
            'book' => 'book',
            'chapter' => 'file',
            'docs' => 'question-sign',
            'generate' => 'gift',
            'i/backup' => 'download',
            't/backup' => 'download',
            'i/checkpermissions' => 'user',
            'i/edit' => 'pencil',
            'i/filter' => 'filter',
            'i/grades' => 'grades',
            'i/group' => 'user',
            'i/hide' => 'eye-open',
            'i/import' => 'upload',
            'i/info' => 'info',
            'i/move_2d' => 'move',
            'i/navigationitem' => 'chevron-right',
            'i/publish' => 'globe',
            'i/reload' => 'refresh',
            'i/report' => 'list-alt',
            'i/restore' => 'upload',
            't/restore' => 'upload',
            'i/return' => 'repeat',
            'i/roles' => 'user',
            'i/settings' => 'cog',
            'i/show' => 'eye-close',
            'i/switchrole' => 'user',
            'i/user' => 'user',
            'i/users' => 'user',
            'spacer' => 'spacer',
            't/add' => 'plus',
            't/copy' => 'plus-sign',
            't/delete' => 'remove',
            't/down' => 'arrow-down',
            't/edit' => 'edit',
            't/editstring' => 'tag',
            't/hide' => 'eye-open',
            't/left' => 'arrow-left',
            't/move' => 'resize-vertical',
            't/right' => 'arrow-right',
            't/show' => 'eye-close',
            't/switch_minus' => 'minus-sign',
            't/switch_plus' => 'plus-sign',
            't/up' => 'arrow-up',
        );
        if (isset($icons[$name])) {
            return '<span class="glyphicon glyphicon-'.$icons[$name].'"></span> ';
        } else {
            return false;
        }
    }

    public function navbar_button_reader($dataid = '#region-main', $class = null) {
        $icon = html_writer::tag('span', '' , array('class' => 'glyphicon glyphicon-zoom-in'));
        $content = html_writer::link('#', $icon . ' ' . get_string('reader','theme_tsc_2013'),
            array('class' => 'btn btn-default navbar-btn btn-sm moodlereader pull-right ' . $class,
                'dataid' => $dataid));
        return $content;
    }

    public function box($contents, $classes = 'generalbox', $id = null, $attributes = array()) {
        if (isset($attributes['data-rel']) && $attributes['data-rel'] === 'fatalerror') {
            return html_writer::div($contents, 'alert alert-danger', $attributes);
        }
        return parent::box($contents, $classes, $id, $attributes);
    }

}

function theme_tsc_2013_get_nav_links($course, $sections, $sectionno) {
        // FIXME: This is really evil and should by using the navigation API.
        $courseformat = course_get_format($course);
        $course = $courseformat->get_course();
        $previousarrow= '<i class="fa fa-chevron-circle-left"></i>';
        $nextarrow= '<i class="fa fa-chevron-circle-right"></i>';
        $canviewhidden = has_capability('moodle/course:viewhiddensections', context_course::instance($course->id))
            or !$course->hiddensections;

        $links = array('previous' => '', 'next' => '');
        $back = $sectionno - 1;
        while ($back > 0 and empty($links['previous'])) {
            if ($canviewhidden || $sections[$back]->uservisible) {
                $params = array('id' => 'previous_section');
                if (!$sections[$back]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $previouslink = html_writer::start_tag('div', array('class' => 'nav_icon'));
                $previouslink .= $previousarrow;
                $previouslink .= html_writer::end_tag('div');
                $previouslink .= html_writer::start_tag('span', array('class' => 'text'));
                $previouslink .= html_writer::start_tag('span', array('class' => 'nav_guide'));
                $previouslink .= get_string('previoussection', 'theme_tsc_2013');
                $previouslink .= html_writer::end_tag('span');
                $previouslink .= html_writer::empty_tag('br');
                $previouslink .= $courseformat->get_section_name($sections[$back]);
                $previouslink .= html_writer::end_tag('span');
                $links['previous'] = html_writer::link(course_get_url($course, $back), $previouslink, $params);
            }
            $back--;
        }

        $forward = $sectionno + 1;
        while ($forward <= $course->numsections and empty($links['next'])) {
            if ($canviewhidden || $sections[$forward]->uservisible) {
                $params = array('id' => 'next_section');
                if (!$sections[$forward]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $nextlink = html_writer::start_tag('div', array('class' => 'nav_icon'));
                $nextlink .= $nextarrow;
                $nextlink .= html_writer::end_tag('div');
                $nextlink .= html_writer::start_tag('span', array('class' => 'text'));
                $nextlink .= html_writer::start_tag('span', array('class' => 'nav_guide'));
                $nextlink .= get_string('nextsection', 'theme_tsc_2013');
                $nextlink .= html_writer::end_tag('span');
                $nextlink .= html_writer::empty_tag('br');
                $nextlink .= $courseformat->get_section_name($sections[$forward]);
                $nextlink .= html_writer::end_tag('span');
                $links['next'] = html_writer::link(course_get_url($course, $forward), $nextlink, $params);
            }
            $forward++;
        }

        return $links;
    }


include_once($CFG->dirroot . "/course/format/topics/renderer.php");
class theme_tsc_2013_format_topics_renderer extends format_topics_renderer {

    protected function get_nav_links($course, $sections, $sectionno) {
        return theme_tsc_2013_get_nav_links($course, $sections, $sectionno);
    }

    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        global $PAGE;

        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();

        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection))) {
            // This section doesn't exist
            print_error('unknowncoursesection', 'error', null, $course->fullname);
            return;
        }

        if (!$sectioninfo->uservisible) {
            if (!$course->hiddensections) {
                echo $this->start_section_list();
                echo $this->section_hidden($displaysection);
                echo $this->end_section_list();
            }
            // Can't view this section.
            return;
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);
        $thissection = $modinfo->get_section_info(0);
        if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
            echo $this->start_section_list();
            echo $this->section_header($thissection, $course, true, $displaysection);
            echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
            echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);
            echo $this->section_footer();
            echo $this->end_section_list();
        }

        // Start single-section div
        echo html_writer::start_tag('div', array('class' => 'single-section'));

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
        $sectiontitle = '';
        $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation header headingblock'));
        // Title attributes
        $titleattr = 'title';
        if (!$thissection->visible) {
            $titleattr .= ' dimmed_text';
        }
        $sectiontitle .= html_writer::tag('div', get_section_name($course, $displaysection), array('class' => $titleattr));
        $sectiontitle .= html_writer::end_tag('div');
        echo $sectiontitle;

        // Now the list of sections..
        echo $this->start_section_list();

        echo $this->section_header($thissection, $course, true, $displaysection);
        // Show completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();

        echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
        echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
        echo $this->section_footer();
        echo $this->end_section_list();

        // Display section bottom navigation.
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('nav', array('id' => 'section_footer'));
        $sectionbottomnav .= $sectionnavlinks['previous'];
        $sectionbottomnav .= $sectionnavlinks['next'];
        // $sectionbottomnav .= html_writer::tag('div', $this->section_nav_selection($course, $sections, $displaysection), array('class' => 'mdl-align'));
        $sectionbottomnav .= html_writer::empty_tag('br', array('style'=>'clear:both'));
        $sectionbottomnav .= html_writer::end_tag('nav');
        echo $sectionbottomnav;

        // Close single-section div.
        echo html_writer::end_tag('div');
    }
}

include_once($CFG->dirroot . "/course/format/weeks/renderer.php");
class theme_tsc_2013_format_weeks_renderer extends format_weeks_renderer {

    protected function get_nav_links($course, $sections, $sectionno) {
        return theme_tsc_2013_get_nav_links($course, $sections, $sectionno);
    }

    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        global $PAGE;

        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();

        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection))) {
            // This section doesn't exist
            print_error('unknowncoursesection', 'error', null, $course->fullname);
            return;
        }

        if (!$sectioninfo->uservisible) {
            if (!$course->hiddensections) {
                echo $this->start_section_list();
                echo $this->section_hidden($displaysection);
                echo $this->end_section_list();
            }
            // Can't view this section.
            return;
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);
        $thissection = $modinfo->get_section_info(0);
        if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
            echo $this->start_section_list();
            echo $this->section_header($thissection, $course, true, $displaysection);
            echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
            echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);
            echo $this->section_footer();
            echo $this->end_section_list();
        }

        // Start single-section div
        echo html_writer::start_tag('div', array('class' => 'single-section'));

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
        $sectiontitle = '';
        $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation header headingblock'));
        // Title attributes
        $titleattr = 'title';
        if (!$thissection->visible) {
            $titleattr .= ' dimmed_text';
        }
        $sectiontitle .= html_writer::tag('div', get_section_name($course, $displaysection), array('class' => $titleattr));
        $sectiontitle .= html_writer::end_tag('div');
        echo $sectiontitle;

        // Now the list of sections..
        echo $this->start_section_list();

        echo $this->section_header($thissection, $course, true, $displaysection);
        // Show completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();

        echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
        echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
        echo $this->section_footer();
        echo $this->end_section_list();

        // Display section bottom navigation.
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('nav', array('id' => 'section_footer'));
        $sectionbottomnav .= $sectionnavlinks['previous'];
        $sectionbottomnav .= $sectionnavlinks['next'];
        // $sectionbottomnav .= html_writer::tag('div', $this->section_nav_selection($course, $sections, $displaysection), array('class' => 'mdl-align'));
        $sectionbottomnav .= html_writer::empty_tag('br', array('style'=>'clear:both'));
        $sectionbottomnav .= html_writer::end_tag('nav');
        echo $sectionbottomnav;

        // Close single-section div.
        echo html_writer::end_tag('div');
    }
}

// Requires V2.6.1.3+ of Collapsed Topics format.
if (file_exists("$CFG->dirroot/course/format/topcoll/renderer.php")) {
    include_once($CFG->dirroot . "/course/format/topcoll/renderer.php");
    class theme_tsc_2013_format_topcoll_renderer extends format_topcoll_renderer {

        protected function get_nav_links($course, $sections, $sectionno) {
            return theme_tsc_2013_get_nav_links($course, $sections, $sectionno);
        }

        public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
            global $PAGE;

            $modinfo = get_fast_modinfo($course);
            $course = course_get_format($course)->get_course();

            // Can we view the section in question?
            if (!($sectioninfo = $modinfo->get_section_info($displaysection))) {
                // This section doesn't exist
                print_error('unknowncoursesection', 'error', null, $course->fullname);
                return;
            }

            if (!$sectioninfo->uservisible) {
                if (!$course->hiddensections) {
                    echo $this->start_section_list();
                    echo $this->section_hidden($displaysection);
                    echo $this->end_section_list();
                }
                // Can't view this section.
                return;
            }

            // Copy activity clipboard..
            echo $this->course_activity_clipboard($course, $displaysection);
            $thissection = $modinfo->get_section_info(0);
            if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
                echo $this->start_section_list();
                echo $this->section_header($thissection, $course, true, $displaysection);
                echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
                echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);
                echo $this->section_footer();
                echo $this->end_section_list();
            }

            // Start single-section div
            echo html_writer::start_tag('div', array('class' => 'single-section'));

            // The requested section page.
            $thissection = $modinfo->get_section_info($displaysection);

            // Title with section navigation links.
            $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
            $sectiontitle = '';
            $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation header headingblock'));
            // Title attributes
            $titleattr = 'title';
            if (!$thissection->visible) {
                $titleattr .= ' dimmed_text';
            }
            $sectiontitle .= html_writer::tag('div', get_section_name($course, $displaysection), array('class' => $titleattr));
            $sectiontitle .= html_writer::end_tag('div');
            echo $sectiontitle;

            // Now the list of sections..
            echo $this->start_section_list();

            echo $this->section_header($thissection, $course, true, $displaysection);
            // Show completion help icon.
            $completioninfo = new completion_info($course);
            echo $completioninfo->display_help_icon();

            echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
            echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
            echo $this->section_footer();
            echo $this->end_section_list();

            // Display section bottom navigation.
            $sectionbottomnav = '';
            $sectionbottomnav .= html_writer::start_tag('nav', array('id' => 'section_footer'));
            $sectionbottomnav .= $sectionnavlinks['previous'];
            $sectionbottomnav .= $sectionnavlinks['next'];
            // $sectionbottomnav .= html_writer::tag('div', $this->section_nav_selection($course, $sections, $displaysection), array('class' => 'mdl-align'));
            $sectionbottomnav .= html_writer::empty_tag('br', array('style'=>'clear:both'));
            $sectionbottomnav .= html_writer::end_tag('nav');
            echo $sectionbottomnav;

            // Close single-section div.
            echo html_writer::end_tag('div');
        }
    }
}

if (file_exists("$CFG->dirroot/course/format/grid/renderer.php")) {
    include_once($CFG->dirroot . "/course/format/grid/renderer.php");
    class theme_tsc_2013_format_grid_renderer extends format_grid_renderer {

        protected function get_nav_links($course, $sections, $sectionno) {
            return theme_tsc_2013_get_nav_links($course, $sections, $sectionno);
        }

        public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
            global $PAGE;

            $modinfo = get_fast_modinfo($course);
            $course = course_get_format($course)->get_course();

            // Can we view the section in question?
            if (!($sectioninfo = $modinfo->get_section_info($displaysection))) {
                // This section doesn't exist
                print_error('unknowncoursesection', 'error', null, $course->fullname);
                return;
            }

            if (!$sectioninfo->uservisible) {
                if (!$course->hiddensections) {
                    echo $this->start_section_list();
                    echo $this->section_hidden($displaysection);
                    echo $this->end_section_list();
                }
                // Can't view this section.
                return;
            }

            // Copy activity clipboard..
            echo $this->course_activity_clipboard($course, $displaysection);
            $thissection = $modinfo->get_section_info(0);
            if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
                echo $this->start_section_list();
                echo $this->section_header($thissection, $course, true, $displaysection);
                echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
                echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);
                echo $this->section_footer();
                echo $this->end_section_list();
            }

            // Start single-section div
            echo html_writer::start_tag('div', array('class' => 'single-section'));

            // The requested section page.
            $thissection = $modinfo->get_section_info($displaysection);

            // Title with section navigation links.
            $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
            $sectiontitle = '';
            $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation header headingblock'));
            // Title attributes
            $titleattr = 'title';
            if (!$thissection->visible) {
                $titleattr .= ' dimmed_text';
            }
            $sectiontitle .= html_writer::tag('div', get_section_name($course, $displaysection), array('class' => $titleattr));
            $sectiontitle .= html_writer::end_tag('div');
            echo $sectiontitle;

            // Now the list of sections..
            echo $this->start_section_list();

            echo $this->section_header($thissection, $course, true, $displaysection);
            // Show completion help icon.
            $completioninfo = new completion_info($course);
            echo $completioninfo->display_help_icon();

            echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
            echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
            echo $this->section_footer();
            echo $this->end_section_list();

            // Display section bottom navigation.
            $sectionbottomnav = '';
            $sectionbottomnav .= html_writer::start_tag('nav', array('id' => 'section_footer'));
            $sectionbottomnav .= $sectionnavlinks['previous'];
            $sectionbottomnav .= $sectionnavlinks['next'];
            // $sectionbottomnav .= html_writer::tag('div', $this->section_nav_selection($course, $sections, $displaysection), array('class' => 'mdl-align'));
            $sectionbottomnav .= html_writer::empty_tag('br', array('style'=>'clear:both'));
            $sectionbottomnav .= html_writer::end_tag('nav');
            echo $sectionbottomnav;

            // Close single-section div.
            echo html_writer::end_tag('div');
        }
    }
}

if (file_exists("$CFG->dirroot/course/format/noticebd/renderer.php")) {
    include_once($CFG->dirroot . "/course/format/noticebd/renderer.php");
    class theme_tsc_2013_format_noticebd_renderer extends format_noticebd_renderer {

        protected function get_nav_links($course, $sections, $sectionno) {
            return theme_tsc_2013_get_nav_links($course, $sections, $sectionno);
        }

        public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
            global $PAGE;

            $modinfo = get_fast_modinfo($course);
            $course = course_get_format($course)->get_course();

            // Can we view the section in question?
            if (!($sectioninfo = $modinfo->get_section_info($displaysection))) {
                // This section doesn't exist
                print_error('unknowncoursesection', 'error', null, $course->fullname);
                return;
            }

            if (!$sectioninfo->uservisible) {
                if (!$course->hiddensections) {
                    echo $this->start_section_list();
                    echo $this->section_hidden($displaysection);
                    echo $this->end_section_list();
                }
                // Can't view this section.
                return;
            }

            // Copy activity clipboard..
            echo $this->course_activity_clipboard($course, $displaysection);

            // General section if non-empty.
            $thissection = $sections[0];
            //if ($thissection->summary or $thissection->sequence or $PAGE->user_is_editing()) {
                echo $this->start_section_list();
                echo $this->section_header($thissection, $course, true, $displaysection);
                $this->print_noticeboard($course);
                if (($PAGE->user_is_editing()) && (is_siteadmin($USER))) {
                    print_section($course, $thissection, $mods, $modnamesused, true, "100%", false, $displaysection);
                    print_section_add_menus($course, 0, $modnames, false, false, $displaysection);
                }
                echo $this->section_footer();
                echo $this->end_section_list();
            //}

            // Start single-section div
            echo html_writer::start_tag('div', array('class' => 'single-section'));

            // The requested section page.
            $thissection = $modinfo->get_section_info($displaysection);

            // Title with section navigation links.
            $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
            $sectiontitle = '';
            $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation header headingblock'));
            // Title attributes
            $titleattr = 'title';
            if (!$thissection->visible) {
                $titleattr .= ' dimmed_text';
            }
            $sectiontitle .= html_writer::tag('div', get_section_name($course, $displaysection), array('class' => $titleattr));
            $sectiontitle .= html_writer::end_tag('div');
            echo $sectiontitle;

            // Now the list of sections..
            echo $this->start_section_list();

            echo $this->section_header($thissection, $course, true, $displaysection);
            // Show completion help icon.
            $completioninfo = new completion_info($course);
            echo $completioninfo->display_help_icon();

            echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
            echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
            echo $this->section_footer();
            echo $this->end_section_list();

            // Display section bottom navigation.
            $sectionbottomnav = '';
            $sectionbottomnav .= html_writer::start_tag('nav', array('id' => 'section_footer'));
            $sectionbottomnav .= $sectionnavlinks['previous'];
            $sectionbottomnav .= $sectionnavlinks['next'];
            // $sectionbottomnav .= html_writer::tag('div', $this->section_nav_selection($course, $sections, $displaysection), array('class' => 'mdl-align'));
            $sectionbottomnav .= html_writer::empty_tag('br', array('style'=>'clear:both'));
            $sectionbottomnav .= html_writer::end_tag('nav');
            echo $sectionbottomnav;

            // Close single-section div.
            echo html_writer::end_tag('div');
        }
    }
}

// Requires V2.6.1.1+ of Columns format.
if (file_exists("$CFG->dirroot/course/format/columns/renderer.php")) {
    include_once($CFG->dirroot . "/course/format/columns/renderer.php");
    class theme_tsc_2013_format_columns_renderer extends format_columns_renderer {

        protected function get_nav_links($course, $sections, $sectionno) {
            return theme_tsc_2013_get_nav_links($course, $sections, $sectionno);
        }

        public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
            global $PAGE;

            $modinfo = get_fast_modinfo($course);
            $course = course_get_format($course)->get_course();

            // Can we view the section in question?
            if (!($sectioninfo = $modinfo->get_section_info($displaysection))) {
                // This section doesn't exist
                print_error('unknowncoursesection', 'error', null, $course->fullname);
                return;
            }

            if (!$sectioninfo->uservisible) {
                if (!$course->hiddensections) {
                    echo $this->start_section_list();
                    echo $this->section_hidden($displaysection);
                    echo $this->end_section_list();
                }
                // Can't view this section.
                return;
            }

            // Copy activity clipboard..
            echo $this->course_activity_clipboard($course, $displaysection);
            $thissection = $modinfo->get_section_info(0);
            if ($thissection->summary or !empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
                echo $this->start_section_list();
                echo $this->section_header($thissection, $course, true, $displaysection);
                echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
                echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);
                echo $this->section_footer();
                echo $this->end_section_list();
            }

            // Start single-section div
            echo html_writer::start_tag('div', array('class' => 'single-section'));

            // The requested section page.
            $thissection = $modinfo->get_section_info($displaysection);

            // Title with section navigation links.
            $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
            $sectiontitle = '';
            $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation header headingblock'));
            // Title attributes
            $titleattr = 'title';
            if (!$thissection->visible) {
                $titleattr .= ' dimmed_text';
            }
            $sectiontitle .= html_writer::tag('div', get_section_name($course, $displaysection), array('class' => $titleattr));
            $sectiontitle .= html_writer::end_tag('div');
            echo $sectiontitle;

            // Now the list of sections..
            echo $this->start_section_list();

            echo $this->section_header($thissection, $course, true, $displaysection);
            // Show completion help icon.
            $completioninfo = new completion_info($course);
            echo $completioninfo->display_help_icon();

            echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
            echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
            echo $this->section_footer();
            echo $this->end_section_list();

            // Display section bottom navigation.
            $sectionbottomnav = '';
            $sectionbottomnav .= html_writer::start_tag('nav', array('id' => 'section_footer'));
            $sectionbottomnav .= $sectionnavlinks['previous'];
            $sectionbottomnav .= $sectionnavlinks['next'];
            // $sectionbottomnav .= html_writer::tag('div', $this->section_nav_selection($course, $sections, $displaysection), array('class' => 'mdl-align'));
            $sectionbottomnav .= html_writer::empty_tag('br', array('style'=>'clear:both'));
            $sectionbottomnav .= html_writer::end_tag('nav');
            echo $sectionbottomnav;

            // Close single-section div.
            echo html_writer::end_tag('div');
        }
    }
}
