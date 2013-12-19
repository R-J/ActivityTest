<?php if (!defined('APPLICATION')) exit();
 
$PluginInfo['ActivityTest'] = array(
   'Name' => 'Activity Test',
   'Description' => 'Test Activity and Notification Feature',
   'Version' => '0.1',
   'Author' => 'Robin'
);

class ActivityTestPlugin extends Gdn_Plugin {
   public function Setup() {
      $Sql = Gdn::SQL();
      // check if there is already an activity type for YourActivity
      if ($Sql->GetWhere('ActivityType', array('Name' => 'ActivityTest'))->NumRows() == 0) {
         // if not, create it.
         $Sql->Insert('ActivityType', // table to insert into
            array('Name' => 'ActivityTest',
               'AllowComments' => '0', // allow users to comment below activities
               'FullHeadline' => '%1$s looked at a %8$s.', // the message to show. See below for the meaning of the variables. Not sure when Full- and when ProfileHeadline is shown
               'ProfileHeadline' => 'ProfileHeadline: %1$s %2$s %3$s %4$s %5$s %6$s %7$s %8$s.',
               'RouteCode' => 'Discussion', // Name of the link (Route) that could be passed to the activity
               'Notify' => '1', // maybe the default behaviour?
               'Public' => '1' // 1 will make it a wall post that anyone can see
            ));
      }
      SaveToConfig('Preferences.Popup.ActivityTest', TRUE);
      SaveToConfig('Preferences.Email.ActivityTest', TRUE);
   }
   public function ProfileController_AfterPreferencesDefined_Handler($Sender) {
      $Sender->Preferences['Notifications']['Popup.ActivityTest'] = T('Notify me when test event happens');
      $Sender->Preferences['Notifications']['Email.ActivityTest'] = T('Notify me when test event happens');
   }
   
   public function DiscussionController_Render_Before($DiscussionController) {
      $DiscussionID = $DiscussionController->DiscussionID;
      $ActivityUserID = Gdn::Session()->UserID; // current user
      
      $ActivityType = 'ActivityTest';
      $Story = 'Some additional text to display under the Headline'; // use default Story
      $RegardingUserID = 1; // in this case the admin
      $CommentActivityID = '';
      $Route = '/discussion/'.$DiscussionID;
      $SendEmail = ''; // use default setting

      $ActivityModel = new ActivityModel();
      $ActivityID = $ActivityModel->Add(
         $ActivityUserID,
         $ActivityType,
         $Story,
         $RegardingUserID,
         $CommentActivityID,
         $Route,
         $SendEmail
      );
      $ActivityModel->SendNotification($ActivityID, $Story);
   }
}
