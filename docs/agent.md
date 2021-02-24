### Agent API functions:
- version - shows version and build of the API, along with the date/time
- external_hangup - sends command to hangup the current phone call for one specific agent(Hangup Customer)
- external_status - sends command to set the disposition for one specific agent and move on to next call
- external_pause - sends command to pause/resume an agent now if not on a call, or pause after their next call if on call
- external_dial - sends command to manually dial a number on the agent's screen
- preview_dial_action - sends a SKIP, DIALONLY, ALTDIAL, ADR3DIAL or FINISH when a lead is being previewed or manual alt dial
- external_add_lead - Adds a lead in the manual dial list of the campaign for logged-in agent
- change_ingroups - changes the selected in-groups for a logged-in agent
- update_fields - changes values for selected data fields in the agent interface
- set_timer_action - sets timer action for the current call the agent is on
- st_login_log - looks up the vicidial_users.custom_three field and logs event from CRM
- st_get_agent_active_lead - looks up active lead info for an agent and outputs lead information
- ra_call_control - remote agent call control: hangup/transfer calls being handled by remote agents
- send_dtmf - sends dtmf signal string to agent's session
- transfer_conference - sends several commands related to the agent transfer-conf frame
- park_call - sends command to park customer or grab customer from park or ivr
- logout - logs the agent out of the agent interface
- recording - sends a recording start/stop signal or status of agent recording
- webserver - display webserver information, very useful for load balanced setups
- webphone_url - display the webphone url for the current agent's session
- call_agent - send a call to connect the agent to their session
- pause_code - set a pause code if the agent is paused
- audio_playback - basic play/pause/resume/stop/restart audio in agent session
- switch_lead - for inbound calls, switches lead_id of live inbound call on agent screen
- calls_in_queue_count - display a count of the calls waiting in queue for the specific agent
- force_fronter_leave_3way - will send a command to fronter agent to leave-3way call that executing agent is on
- force_fronter_audio_stop - will send a command to fronter agent session to stop any audio_playback playing on it
- vm_message - set a custom voicemail message to be played when agent clicks the VM button on the agent screen


### Required variables for all API calls:
- user - is the API user
- pass - is the API user password
- agent_user - is the vicidial agent user whose session that you want to affect
- source - description of what originated the API call (maximum 20 characters)

### Optional variable for all API calls:
- close_window_link - will display a link to close the window, useful if you pop up the API link in a browser window
- language - currently only works for close window link: en=English, es=Spanish



To hangup the call, disposition it and then pause the agent, do the following in order:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=external_pause&value=PAUSE
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=external_hangup&value=1
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=external_status&value=A



Response to calls will return either an ERROR or a SUCCESS along with an explanation.
for example:
SUCCESS: external_status function set - 6666|A
ERROR: agent_user is not logged in - 6666
ERROR: auth USER DOES NOT HAVE PERMISSION TO USE THIS FUNCTION - 6666|webserver|ADMIN



DETAIL OF EACH FUNCTION:



--------------------------------------------------------------------------------
version -

DESCRIPTION:
shows version and build of the API, along with the date/time

VALUES: NONE

EXAMPLE URL:
http://server/agc/api.php?function=version

RESPONSES:
VERSION: 2.0.5-2|BUILD: 90116-1229|DATE: 2009-01-15 14:59:33|EPOCH: 1222020803 



--------------------------------------------------------------------------------
webserver -

DESCRIPTION:
shows version and build of the API, along with the date/time

VALUES: NONE

EXAMPLE URL:
http://server/agc/api.php?source=test&user=6666&pass=1234&function=webserver

RESPONSES:
Webserver Data:
set.timezone: America/New_York
abbr.timezone: EDT
dst.timezone: 1
uname: Linux dev-db 2.6.34.8-0.2-default #1 SMP 2011-04-06 18:11:26 +0200 x86_64
host name: dev-db
server name: www.vicidial.org
php version: 5.3.3
apache version: Apache/2.2.15 (Linux/SUSE)
apache processes: 13
system loadavg: 0.06
disk_free_space: 444174315520
date.timezone: America/New_York
max_execution_time: 360
max_input_time: 360
memory_limit: 128M
post_max_size: 48M
upload_max_filesize: 42M
default_socket_timeout: 360




--------------------------------------------------------------------------------
external_hangup - 

DESCRIPTION:
Hangs up the current customer call on the agent screen

VALUES: (value)
1  - the only valid value for this function

EXAMPLE URL:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=external_hangup&value=1

RESPONSES:
ERROR: external_hangup not valid - 1|6666
ERROR: no user found - 6666
ERROR: agent_user is not logged in - 6666
SUCCESS: external_hangup function set - 1|6666



--------------------------------------------------------------------------------
external_status - 

DESCRIPTION:
Sets the status of the current customer call on the agent dispotion screen

VALUES: (value)
value - 
 Any valid status in the VICIDIAL system will work for this function
callback_datetime -
 YYYY-MM-DD+HH:MM:SS, date and time of scheduled callback. REQUIRED if callback is set and status is flagged as a scheduled callback
callback_type -	
 USERONLY or ANYONE, default is ANYONE
callback_comments -
 Optional comments to appear when the callback is called back, must be less than 200 characters in length
qm_dispo_code - 
 Option callstatus code used if QM is enabled

EXAMPLE URL:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=external_status&value=A
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=external_status&value=CALLBK&callback_datetime=2012-01-25+12:00:00&callback_type=USERONLY&callback_comments=callback+comments+go+here&qm_dispo_code=1234

RESPONSES:
ERROR: external_status not valid - A|6666
ERROR: no user found - 6666
ERROR: agent_user is not logged in - 6666
SUCCESS: external_status function set - A|6666



--------------------------------------------------------------------------------
external_pause - 

DESCRIPTION:
Pauses or Resumes the agent. If a Pause and the agent is on a live call will pause after the live call is dispositioned

VALUES: (value)
PAUSE  - Pauses the agent session
RESUME  - Resumes the agent session

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=external_pause&value=PAUSE
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=external_pause&value=RESUME

RESPONSES:
ERROR: external_pause not valid - PAUSE|6666
ERROR: no user found - 6666
ERROR: agent_user is not logged in - 6666
SUCCESS: external_pause function set - PAUSE|1232020456|6666



--------------------------------------------------------------------------------
logout - 

DESCRIPTION:
Logs the agent out of the agent interface. If the agent is on a live call, will logout after the live call is dispositioned

VALUES: (value)
LOGOUT  - Logout the agent session


EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=logout&value=LOGOUT

RESPONSES:
ERROR: logout not valid - PAUSE|6666
ERROR: no user found - 6666
ERROR: agent_user is not logged in - 6666
SUCCESS: logout function set - LOGOUT|1232020456|6666



--------------------------------------------------------------------------------
external_dial - 

DESCRIPTION:
Places a manual dial phone call on the agent screen, you can define whether to search for the lead in the existing database or not and you can define the phone_code and the number to dial. This action will pause the agent after their current call, enter in the information to place the call, and dialing the call on the agent screen.

VALUES:
value - 
 Any valid phone number (7275551212), or "MANUALNEXT" to mimic the Dial Next Number button
lead_id -
 Any valid lead_id from the system(either value or lead_id are required) if both are defined, lead_id will override value
phone_code -
 Any valid phone country code (1 for USA/Canada, 44 for UK, etc...)
search -
 YES  - perform a search in the campaign-defined vicidial_list list for this phone number and bring up that lead
 NO  - do not search, create a new vicidial_list record for the call
preview -
 YES  - preview the lead in the vicidial screen without dialing
 NO  - do not preview the lead, place call immediately
focus -
 YES  - change the focus of the screen to the vicidial.php agent interface, brings up an alert in the browser window
 NO  - do not change focus
vendor_id -
 OPTIONAL, any valid Vendor lead code
dial_prefix -
 OPTIONAL, any dial prefix that you want to add to the beginning of the dial string for this call
group_alias - 
 OPTIONAL, the outbound callerID(from an existing group-alias) that you want to use for this call
vtiger_callback -
 OPTIONAL, YES or NO, will lookup the phone number and Vtiger account ID from the provided Event ID
alt_user -
 OPTIONAL, instead of agent_user, this is to lookup the agent_user using the vicidial_users.custom_three field
alt_dial -
 OPTIONAL, if using lead_id you can set this flag to dial the ALT number or the ADDR3 number or SEARCH a phone_number within the lead
           if SEARCH is used and the phone_number is not matched with the lead's phone_number, alt_phone or address3 field
	   an ERROR will be returned
dial_ingroup -
 OPTIONAL, place the call as an in-group outbound calls
outbound_cid - 
 OPTIONAL, the CallerID to send for this outbound call. NOTE: This will only work if "Outbound Call Any CID" is enabled in System Settings!


EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=external_dial&value=7275551212&phone_code=1&search=YES&preview=NO&focus=YES
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=external_dial&value=7275551212&phone_code=1&search=YES&preview=NO&focus=YES&dial_prefix=88&group_alias=DEFAULT

RESPONSES:
NOTICE: defined dial_ingroup not found - FAKE_INGROUP
ERROR: external_dial not valid - 7275551212|1|YES|6666
ERROR: no user found - 6666
ERROR: agent_user is not logged in - 6666
ERROR: agent_user is not allowed to place manual dial calls - 6666
ERROR: caller_id_number from group_alias is not valid - 6666|TESTING|123
ERROR: group_alias is not valid - 6666|TESTING
ERROR: outbound_cid is not allowed on this system - 6666|3125551212|DISABLED
ERROR: vtiger callback activity does not exist in vtiger system - 12345
ERROR: phone_number is already in this agents manual dial queue - 6666|7275551211
ERROR: lead_id is not valid - 6666|1234567
ERROR: phone number is not valid - 6666||1234567|
ERROR: phone number lead_id search not found - 6666|7275551212|1234567|
SUCCESS: external_dial function set - 7275551212|6666|1|YES|NO|YES|123456|1232020456|9|TESTING|7275551211|





--------------------------------------------------------------------------------
preview_dial_action - 

DESCRIPTION:
sends a SKIP, DIALONLY, ALTDIAL, ADR3DIAL or FINISH when a lead is being previewed or in Manual Alt Dial

VALUES:
agent_user -
 REQUIRED alphanumeric string for active agent user

value - 
 One of the following actions (SKIP, DIALONLY, ALTDIAL, ADR3DIAL or FINISH)

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=preview_dial_action&value=SKIP

RESPONSES:
ERROR: preview_dial_action not valid - 7275551212|1|YES|6666
ERROR: no user found - 6666
ERROR: agent_user is not logged in - 6666
ERROR: agent_user is not allowed to place manual dial calls - 6666
ERROR: preview dialing not allowed on this campaign - 6666|TESTING|DISABLED
ERROR: preview dial skipping not allowed on this campaign - 6666|TESTING|PREVIEW_ONLY
ERROR: alt number dialing not allowed on this campaign - 6666|TESTING|N
SUCCESS: preview_dial_action function set - DIALONLY|6666|DIALONLY





--------------------------------------------------------------------------------
external_add_lead - 

DESCRIPTION:
Adds a lead in the manual dial list of the campaign for logged-in agent. A much simplified add lead function compared to the Non-Agent API function

VALUES:
agent_user -
 REQUIRED alphanumeric string for agent user
dnc_check - 
 OPTIONAL - Check for number against system DNC
campaign_dnc_check - 
 OPTIONAL - Check for number against campaign DNC from the agent's campaign
LEAD DATA (must populate at least one)
   NOTE: Only fields that are specified in the API call will be modified
	address1
	address2
	address3
	alt_phone
	city
	comments
	country_code
	date_of_birth
	email
	first_name
	gender
	gmt_offset_now
	last_name
	middle_initial
	phone_number
	phone_code
	postal_code
	province
	security_phrase
	source_id
	state
	title
	vendor_lead_code
	rank
	owner

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=external_add_lead&phone_number=7275551212&phone_code=1&first_name=Bob&last_name=Smith=NO&dnc_check=YES

RESPONSES:
ERROR: external_add_lead not valid - 7275551212|1|6666|
ERROR: no user found - 6666
ERROR: lead insertion failed - 7275551212|TESTCAMP|101|6666
ERROR: add_lead PHONE NUMBER IN DNC - 7275551212|6666
ERROR: add_lead PHONE NUMBER IN CAMPAIGN DNC - 7275551212|TESTCAMP|6666
ERROR: campaign manual dial list undefined - 7275551212|TESTCAMP|6666
ERROR: agent_user is not logged in - 6666
SUCCESS: lead added - 7275551212|TESTCAMP|101|123456|6666





--------------------------------------------------------------------------------
change_ingroups - 

DESCRIPTION:
This function will change the selected in-groups for an agent that is logged into a campaign that allows for inbound calls to be handled. Allows the selected in-groups for an agent to be changed while they are logged-in to the ViciDial Agent screen only. Once changed in this way, the agent would need to log out and back in to be able to select in-groups themselves(If Agent Choose In-Groups is enabled for that user). The blended checkbox can also be changed using this function. The API user performing this function must have vicidial_users.change_agent_campaign = 1.

VALUES:
value -
 CHANGE  - will change all in-groups to those defined in ingroup_choices
 REMOVE  - will only remove the listed in-groups
 ADD  - will only add the listed in-groups
blended - 
 YES  - set the agent to take outbound auto-dialed calls (not applicable in MANUAL and INBOUND_MAN dial method campaigns)
 NO  - set the agent to only take inbound calls
ingroup_choices - 
 OPTIONAL, a space-delimited(use plusses + in the URL) list of in-groups to allow the agent to take calls from, example: " TEST_IN2 SALESLINE TRAINING_IN -"
set_as_default - 
 OPTIONAL, YES or NO - overwrites the settings for the agent in the user modification screen, default is NO

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=change_ingroups&value=CHANGE&set_as_default=YES&blended=YES&ingroup_choices=+TEST_IN+SALESLINE+FAKE_IN+-
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=change_ingroups&value=REMOVE&blended=NO&ingroup_choices=+TEST_IN2+TEST_IN4+-
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=change_ingroups&value=ADD&blended=NO&ingroup_choices=+TEST_IN2+-

RESPONSES:
ERROR: change_ingroups not valid - N| TEST_IN SALESLINE -
ERROR: agent_user is not logged in - 6666
ERROR: campaign does not allow inbound calls - 6666
ERROR: user is not allowed to change agent in-groups - 6666|TESTING|123
ERROR: campaign dial_method does not allow outbound autodial - 6666|TESTING
ERROR: ingroup does not exist - FAKE_IN| TEST_IN FAKE_IN SALESLINE -
ERROR: ingroup_choices are required for ADD and REMOVE values - ADD|
SUCCESS: change_ingroups function set - YES| TEST_IN SALESLINE -|6666





--------------------------------------------------------------------------------
update_fields -

DESCRIPTION:
Updates the fields that are specified with the values. This will update the data that is on the agent's screen in the customer information section.

VALUES:
agent_user -
 REQUIRED alphanumeric string for agent user
LEAD DATA (must populate at least one)
   NOTE: Only fields that are specified in the API call will be modified
	address1
	address2
	address3
	alt_phone
	city
	comments
	country_code
	date_of_birth
	email
	first_name
	gender
	gmt_offset_now
	last_name
	middle_initial
	phone_number
	phone_code
	postal_code
	province
	security_phrase
	source_id
	state
	title
	vendor_lead_code
	rank
	owner

NOTES:
	- most special characters are not allowed, but single quotes are

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&function=update_fields&agent_user=6666&vendor_lead_code=1234567&address1=

RESPONSES:
ERROR: update_fields not valid - 6666
ERROR: agent_user is not logged in - 6666
ERROR: user is not allowed to modify lead information - 6666|1234
ERROR: agent_user does not have a lead on their screen - 6666|1234
ERROR: no fields have been defined - 6666
SUCCESS: update_fields lead updated - 6666|1234|87498|vendor_lead_code='1234567',address1=''





--------------------------------------------------------------------------------
set_timer_action - 

DESCRIPTION:
Updates the fields that are specified with the values. This will update the data that is on the agent's screen in the customer information section.

VALUES:
agent_user -
 REQUIRED, alphanumeric string for agent user
value -
 REQUIRED, one of these choices: 'NONE','WEBFORM','WEBFORM2','D1_DIAL','D2_DIAL','D3_DIAL','D4_DIAL','D5_DIAL','MESSAGE_ONLY'
notes -
 Optional, the message to be displayed with the timer action
rank - 
 Optional, the number of seconds into the call to display


EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&function=set_timer_action&agent_user=6666&value=MESSAGE_ONLY&notes=test+message&rank=15

RESPONSES:
ERROR: set_timer_action not valid - 6666
ERROR: agent_user is not logged in - 6666
ERROR: user is not allowed to modify campaign settings - 6666|1234
SUCCESS: set_timer_action lead updated - 6666|1234|MESSAGE_ONLY|test message|15





--------------------------------------------------------------------------------
st_login_log - 

DESCRIPTION:
Looks up the vicidial_users.custom_three field(as "agentId") to associate with a vicidial user ID. If found it will populate the custom_four field with a "teamId" value, then output the vicidial user ID

VALUES:
value -
 REQUIRED alphanumeric string for CRM AgentID
vendor_id - 
 REQUIRED alphanumeric string for CRM TeamID

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&function=st_login_log&value=876543&vendor_id=207

RESPONSES:
ERROR: st_login_log not valid - 6666|207
ERROR: no user found - 6666
SUCCESS: st_login_log user found - 6666





--------------------------------------------------------------------------------
st_get_agent_active_lead - 

DESCRIPTION:
Looks up the vicidial_users.custom_three field(as "agentId") to associate with a vicidial user ID. If found it will output the active lead_id and phone number, vendor_lead_code, province, security_phrase and source_id fields.

VALUES:
value -
 REQUIRED alphanumeric string for CRM AgentID
vendor_id - 
 REQUIRED alphanumeric string for CRM TeamID

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&function=st_get_agent_active_lead&value=876543&vendor_id=207

RESPONSES:
ERROR: st_get_agent_active_lead not valid - 6666|207
ERROR: no user found - 6666
ERROR: user not logged in - 6666
ERROR: no active lead found - 6666
SUCCESS: st_get_agent_active_lead lead found - 6666|7275551212|123456|9987-1234765|SK|WILLIAMS|JUH764AJJJ9





--------------------------------------------------------------------------------
ra_call_control - 

DESCRIPTION:
Allows for remote agent call control: hangup/transfer calls being handled by remote agents, also options for recording a disposition and call length

VALUES:
value -
 REQUIRED, The call ID of the call as received as CallerIDname field or a special SIP-header, i.e. Y0315201639000402027
agent_user -
 REQUIRED, alphanumeric string for remote agent user
stage - 
 REQUIRED, one of these choices: 'HANGUP','EXTENSIONTRANSFER','INGROUPTRANSFER'
ingroup_choices - 
 OPTIONAL, only required if INGROUPTRANSFER stage is used, must be a single active in-group, reserved option of "DEFAULTINGROUP" can be used to send the call to the default in-group for the in-group or campaign that originated the call to the remote agent
phone_number -
 OPTIONAL, only required if EXTENSIONTRANSFER stage is used, must be a full number when dialed that will dial through the default context
status -
 OPTIONAL, status of the call, maximum of 6 characters, if not set, status will be RAXFER

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1028&function=ra_call_control&stage=INGROUPTRANSFER&ingroup_choices=DEFAULTINGROUP&value=Y0316001655000402028

RESPONSES:
ERROR: ra_call_control not valid - Y0315201639000402027|6666|INGROUPTRANSFER
ERROR: no user found - 6666
ERROR: user not logged in - 6666
ERROR: no active call found - Y0315201639000402027
ERROR: phone_number is not valid - 9
ERROR: ingroup is not valid - TESTINGROUP
ERROR: stage is not valid - XYZ
SUCCESS: ra_call_control transferred - 6666|Y0315201639000402027|SALESLINE
SUCCESS: ra_call_control hungup - 6666|Y0315201639000402027|HANGUP





--------------------------------------------------------------------------------
send_dtmf - 

DESCRIPTION:
Sends dtmf signal string to agent's session

VALUES: (value)
only valid DTMF characters with these replacements:
  P = # (pound or hash)
  S = * (star)
  Q = (one second of silence)

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=send_dtmf&value=QQQQ1234SQQQQQ6654P

RESPONSES:
ERROR: send_dtmf not valid - QQ|6666
ERROR: no user found - 6666
ERROR: agent_user is not logged in - 6666
SUCCESS: send_dtmf function set - QQQQ1234SQQQQQ6654P|6666





--------------------------------------------------------------------------------
park_call - 

DESCRIPTION:
sends command to park customer or grab customer out of park

VALUES:
 value - 
  REQUIRED, choices are below
   PARK_CUSTOMER - send customer to the park extension as defined in the campaign the agent is logged into
   GRAB_CUSTOMER - grab customer from the park extension and send them to the agent session
   PARK_IVR_CUSTOMER - send customer to the park ivr as defined in the campaign the agent is logged into, customer will come back after finishing IVR
   GRAB_IVR_CUSTOMER - grab customer from the park ivr and send them to the agent session

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=park_call&value=PARK_CUSTOMER
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=park_call&value=GRAB_CUSTOMER
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=park_call&value=PARK_IVR_CUSTOMER
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=park_call&value=GRAB_IVR_CUSTOMER

RESPONSES:
ERROR: park_call not valid - PARK_CUSTOMER|6666
ERROR: no user found - 6666
ERROR: agent_user is not logged in - 6666
ERROR: agent_user does not have a lead on their screen - 6666
SUCCESS: park_call function set - PARK_CUSTOMER|6666





--------------------------------------------------------------------------------
transfer_conference - 

DESCRIPTION:
sends several commands related to the agent transfer-conf frame

VALUES:
 value - 
  REQUIRED, choices are below
   HANGUP_XFER - hangup the third party line
   HANGUP_BOTH - hangup customer and third party line
   BLIND_TRANSFER - send a call to a defined phone number
   LEAVE_VM - blind transfer customer to the campaign-defined voicemail message
   LOCAL_CLOSER - send call to another ViciDial agent, must have in-group, optional phone_number field for AGENTDIRECT agent
   DIAL_WITH_CUSTOMER - 3-way call with customer on the line
   PARK_CUSTOMER_DIAL - send customer to park and place a call to a third party
   LEAVE_3WAY_CALL - leave customer and third party in conference and go to the disposition screen
 phone_number - 
  OPTIONAL/REQUIRED, required for any transfer or dial value
 ingroup_choices - 
  OPTIONAL/REQUIRED, required for local_closer and consultative transfers, must be a single active in-group, reserved option of "DEFAULTINGROUP" can be used to select the default in-group for the in-group or campaign that the call originated from
 consultative -
  OPTIONAL, when you want to do a consultative transfer with your customer and another ViciDial agent, 'YES' and 'NO' are valid options, you can only use this with DIAL_WITH_CUSTOMER or PARK_CUSTOMER_DIAL
 dial_override - 
  OPTIONAL, dials exactly the phone number specified with no campaign-defined phone code or prefix, 'YES' and 'NO' are valid options
 group_alias - 
  OPTIONAL, defines what caller ID number to use when doing DIAL_WITH_CUSTOMER or PARK_CUSTOMER_DIAL
 cid_choice - 
  OPTIONAL, alternate method for caller ID number to use when doing DIAL_WITH_CUSTOMER or PARK_CUSTOMER_DIAL: 'CAMPAIGN','AGENT_PHONE','CUSTOMER','CUSTOM_CID'

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=transfer_conference&value=HANGUP_XFER
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=transfer_conference&value=HANGUP_BOTH
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=transfer_conference&value=BLIND_TRANSFER&phone_number=8500
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=transfer_conference&value=LEAVE_VM
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=transfer_conference&value=LOCAL_CLOSER&ingroup_choices=DEFAULTINGROUP
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=transfer_conference&value=LOCAL_CLOSER&ingroup_choices=AGENTDIRECT&phone_number=6666
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=transfer_conference&value=LOCAL_CLOSER&ingroup_choices=SALESLINE
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=transfer_conference&value=DIAL_WITH_CUSTOMER&ingroup_choices=TEST_IN3&consultative=YES
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=transfer_conference&value=DIAL_WITH_CUSTOMER&phone_number=8500
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=transfer_conference&value=DIAL_WITH_CUSTOMER&phone_number=919998888112&dial_override=YES
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=transfer_conference&value=DIAL_WITH_CUSTOMER&phone_number=919998888112&cid_choice=CUSTOMER
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=transfer_conference&value=PARK_CUSTOMER_DIAL&ingroup_choices=TEST_IN3&consultative=YES
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=transfer_conference&value=LEAVE_3WAY_CALL

RESPONSES:
ERROR: transfer_conference not valid - QQ|6666
ERROR: value is not valid - XYZFUNCTION|6666
ERROR: no user found - 6666
ERROR: ingroup is not valid - XYZINGROUP
ERROR: agent_user is not logged in - 6666
ERROR: agent_user does not have a live call - 6666
ERROR: caller_id_number from group_alias is not valid - 6666|TESTING|123
ERROR: group_alias is not valid - 6666|TESTING
ERROR: cid_choice is not valid - 6666|TESTING
SUCCESS: transfer_conference function set - LOCAL_CLOSER|SALESLINE||YES|6666|M2141842580000000044|





--------------------------------------------------------------------------------
recording - 

DESCRIPTION:
sends a recording start/stop signal or status of agent recording

VALUES:
 value - 
  REQUIRED, choices are below
   START - sends a "start recording" signal to the agent screen
            (you can have multiple recordings going at the same time)
   STOP - sends a "stop recording" signal to the agent screen
            (this will stop all active recordings onthe agent screen)
   STATUS - displays results of active recording and agent session information
            (returns: user|recording_id|filename|server|start_time|agent_server|session|agent_status)
  stage - 
    OPTIONAL, value to append to the recording filename, limited to 14 characters, if more it will truncate. Only works with START value

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=recording&value=START
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=recording&value=STOP
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=recording&value=STATUS
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=recording&value=START&stage=_MIDCALL

RESPONSES:
ERROR: no user found - 6666
ERROR: agent_user is not logged in - 6666 
ERROR: stop recording error - 6666|||||192.168.1.5|8600051|PAUSED 
SUCCESS: recording function sent - 6666|STOP||||192.168.1.5|8600051|PAUSED
SUCCESS: recording function sent - 6666|START||||192.168.1.5|8600051|PAUSED 
SUCCESS: recording function sent - 6666|START_MIDCALL||||192.168.1.5|8600051|PAUSED 
NOTICE: not recording - 6666|||||192.168.1.5|8600051|PAUSED
NOTICE: recording active - 6666|121242|20120810-012008__6666_|192.168.1.5|2012-08-10 01:20:10|192.168.1.5|8600051|PAUSED





--------------------------------------------------------------------------------
webphone_url - 

DESCRIPTION:
display or launch the webphone url for the current agent's session

VALUES:
 value - 
  REQUIRED, choices are below:
   DISPLAY - displays only the URL for the webphone if enabled
   LAUNCH - redirects the url to the webphone url to launch it

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=webphone_url&value=DISPLAY
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=webphone_url&value=LAUNCH

RESPONSES:
<if function is successful, the URL will be displayed or launched>
ERROR: no user found - 6666
ERROR: agent_user is not logged in - 6666 
ERROR: webphone_url not valid - 6666|DISPLAY
ERROR: webphone_url error - webphone url is empty - 6666
ERROR: webphone_url error - no session data - 6666





--------------------------------------------------------------------------------
call_agent - 

DESCRIPTION:
send a call to connect the agent to their session

VALUES:
 value - 
  REQUIRED, choices are below:
   CALL - places call from the agent session to the agent's phone

NOTES:
this function is not designed to work with on-hook agents

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=call_agent&value=CALL

RESPONSES:
ERROR: no user found - 6666
ERROR: agent_user is not logged in - 6666 
ERROR: call_agent not valid - 6666|CALL
ERROR: call_agent error - entry is empty - 6666
ERROR: call_agent error - no session data - 6666
SUCCESS: call_agent function sent - 6666





--------------------------------------------------------------------------------
audio_playback - 

DESCRIPTION:
Basic play/stop/pause/resume/restart audio in agent session

NOTE: PAUSE/RESUME/RESTART features only work with Asterisk 1.8 and higher
       (In Asterisk versions earlier than 1.8 you can replicate RESTART using PLAY and dial_override=Y)

VALUES:
 stage - 
  REQUIRED, choices are below:
   PLAY - starts playing of new audio file in agent session
   STOP - kills playback of audio in agent session
   PAUSE - pauses playing of audio
   RESUME - resumes playing of audio after pause
   RESTART - restarts playback at beginning of audio
 value - 
  REQUIRED for stage of 'PLAY', name of audio file in audio store to play, must NOT have extension
 dial_override - 
  OPTIONAL, (Y or N), default is N. Allows you to PLAY without issuing a STOP to a currently playing audio file


EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=audio_playback&value=ss-noservice&stage=PLAY
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=audio_playback&stage=STOP
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=audio_playback&stage=PAUSE
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=audio_playback&stage=RESUME
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=audio_playback&stage=RESTART
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=audio_playback&value=ss-noservice&stage=PLAY&dial_override=Y

RESPONSES:
ERROR: no user found - 6666
ERROR: agent_user is not logged in - PLAY|6666 
ERROR: audio_playback not valid - ss-noservice|PLAY|6666
ERROR: audio_playback error - entry is empty - PLAY|6666
ERROR: audio_playback error - no session data - PLAY|6666
ERROR: audio_playback error - no audio playing in agent session - 8600051|10.10.10.15|PAUSE|6666
ERROR: audio_playback error - audio already playing in agent session - 8600051|10.10.10.15|PLAY|6666
NOTICE: audio_playback previous playback stopped - PLAY|Y|6666
SUCCESS: audio_playback function sent - ss-noservice|PLAY|6666





--------------------------------------------------------------------------------
switch_lead - 

DESCRIPTION:
For agents on a live inbound call, switches lead_id of live inbound call on agent screen including associated logs. You can define a lead_id or a vendor_lead_code to switch to. Works like the SELECT function of the LEAD SEARCH feature in the agent screen.

VALUES:
lead_id -
 Any valid lead_id from the system(either lead_id or vendor_lead_code are required) if both are defined, lead_id will override vendor_lead_code
vendor_lead_code -
 OPTIONAL, any valid Vendor lead code

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=switch_lead&lead_id=12345
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=switch_lead&vendor_lead_code=1234567890

RESPONSES:
ERROR: switch_lead not valid - ||6666
ERROR: no user found - 6666
ERROR: agent_user is not logged in - 6666
ERROR: agent_user does not have a live call - 6666
ERROR: agent call is not inbound - 6666|V2012367890123456789
ERROR: campaign not found - 6666|TESTCAMP
ERROR: campaign does not allow inbound lead search - 6666|TESTCAMP
ERROR: switch-to lead not found - 6666|12345|123456789
SUCCESS: switch_lead function set - 6666|12345|1234567890|TESTCAMP|12346





--------------------------------------------------------------------------------
vm_message - 

IMPORTANT NOTES: 
- Set the campaign "Answering Machine Message" setting to 'LTTagent' for this to work.
- There are some other campaign settings that can override this function, so you will want to disable 'AM Message Wildcards' and 'VM Message Groups'.

DESCRIPTION:
Set a custom voicemail message to be played when agent clicks the VM button on the agent screen

VALUES:
value -
 REQUIRED, One audio file or multiple audio files(separated by pipes) to play when the call is sent to VM by the agent
lead_id -
 OPTIONAL, The lead_id of the call that the agent is currently on, if populated it will validate that is the lead the agent is talking to

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=vm_message&value=EXship01|EXship02|EXship03
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=vm_message&lead_id=12345&value=appointment_reminder2

RESPONSES:
ERROR: vm_message not valid - ||6666
ERROR: no user found - 6666
ERROR: agent_user is not logged in - 6666
ERROR: current call does not match lead_id submitted - 6666
ERROR: agent_user does not have a live call - 6666
SUCCESS: vm_message function set - 6666|12345|EXship01|EXship02|EXship03





--------------------------------------------------------------------------------
pause_code - 

DESCRIPTION:
set a pause code for an agent that is paused

VALUES:
 value - pause code to set, must be 6 characters or less

NOTES:
this function will not work if the agent is not paused

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=pause_code&value=BREAK

RESPONSES:
ERROR: no user found - 6666
ERROR: agent_user is not logged in - 6666 
ERROR: pause_code not valid - 6666|JUMPING
ERROR: pause_code error - agent is not paused - 6666
SUCCESS: pause_code function sent - 6666





--------------------------------------------------------------------------------
calls_in_queue_count - 

DESCRIPTION:
display a count of the calls waiting in queue for the specific agent

VALUES:
 value - 
  REQUIRED, choices are below:
   DISPLAY - displays number of calls in queue that could be sent to this agent

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=calls_in_queue_count&value=DISPLAY

RESPONSES:
ERROR: no user found - 6666
ERROR: agent_user is not logged in - 6666 
SUCCESS: calls_in_queue_count - 0





--------------------------------------------------------------------------------
force_fronter_leave_3way - 

DESCRIPTION:
will send a command to fronter agent to leave-3way call that executing agent is on. Will not execute command for the named 'agent_user', but will look for oldest other user currently on a call with the same lead_id that the named agent_user is on the phone with.

VALUES:
 value - 
  REQUIRED, choices are below:
   LOCAL_ONLY - looks for fronter only on local cluster
   LOCAL_AND_CCC - looks on local cluster and remote CCC to send command to (will always check local first)
   CCC_REMOTE - use this when the closer is not on this cluster
  lead_id - 
    OPTIONAL, only to be used with CCC_REMOTE value commands

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=force_fronter_leave_3way&value=LOCAL_ONLY
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=force_fronter_leave_3way&value=LOCAL_AND_CCC

RESPONSES:
ERROR: no user found - 6666
ERROR: agent_user is not logged in - 6666
ERROR: agent_user is not on a phone call - 6666
ERROR: no fronter found - 6666
SUCCESS: force_fronter_leave_3way SENT - 6667
SUCCESS: force_fronter_leave_3way command sent over CCC - test_ccc





--------------------------------------------------------------------------------
force_fronter_audio_stop - 

DESCRIPTION:
will send a command to fronter agent session to stop any audio_playback playing on it. Will not execute command for the named 'agent_user', but will look for other user session currently on a call with the same lead_id that the named agent_user is on the phone with.

VALUES:
 value - 
  REQUIRED, choices are below:
   LOCAL_ONLY - looks for fronter only on local cluster
   LOCAL_AND_CCC - looks on local cluster and remote CCC to send command to (will always check local first)
   CCC_REMOTE - use this when the closer is not on this cluster
  lead_id - 
    OPTIONAL, only to be used with CCC_REMOTE value commands

EXAMPLE URLS:
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=force_fronter_audio_stop&value=LOCAL_ONLY
http://server/agc/api.php?source=test&user=6666&pass=1234&agent_user=1000&function=force_fronter_audio_stop&value=LOCAL_AND_CCC

RESPONSES:
ERROR: force_fronter_audio_stop not valid - LOCAL_AND_CCC|6666
ERROR: auth USER DOES NOT HAVE PERMISSION TO USE THIS FUNCTION - LOCAL_AND_CCC|6666|force_fronter_audio_stop
ERROR: no user found - 6666
ERROR: agent_user is not logged in - 6666
ERROR: agent_user is not on a phone call - 6666
ERROR: no fronter session found - |6666|1234
ERROR: no fronter found - 6666
ERROR: force_fronter_audio_stop error - no audio playing in other agent session - 8600051|10.0.0.5||6666|1234
SUCCESS: force_fronter_audio_stop function SENT - LOCAL_AND_CCC||6667|1234
SUCCESS: force_fronter_audio_stop command sent over CCC - test_ccc
