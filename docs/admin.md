# Admin

NON-AGENT API DOCUMENT           Started: 2008-07-24         Updated: 2021-03-03


This document describes the functions of an API(Application Programming 
Interface) for all functions NOT directly relating to the VICIDIAL Agent screen. 
This functionality will be rather limited at first and will be built upon as 
critical functions are identified and programmed into it.

There is also an Agent API script, for more information on that, please read
the AGENT_API.txt document.

NOTE: The API is able to use HTTPS if your webserver is configured to use HTTPS.
	For example, all VICIhost accounts are able to use HTTPS for API functions


API functions:

- version - shows version and build of the API, along with the date/time
- sounds_list - outputs a list of audio files from the audio store
- moh_list - outputs a list of music on hold classes in the system
- vm_list - outputs a list of voicemail boxes in the system
- blind_monitor - calls user-defined phone and places them in session as blind monitor
- add_lead - adds a new lead to the vicidial_list table with several fields and options
- update_lead - updates lead information in vicidial_list and associated custom table
- agent_ingroup_info - shows in-group and outbound auto-dial info for logged-in agent
- recording_lookup - looks up recordings based upon user and date or lead_id
- did_log_export - exports all calls inbound to a DID for one day
- agent_stats_export - exports agent statistics for set time period
- add_user - adds a user to the system
- update_user - updates some user settings in the system
- copy_user - copies an existing user in the system to a new user ID and name
- add_phone - adds a phone to the system
- update_phone - updates or deletes an existing phone record in the system
- add_phone_alias - adds a phone alias record to the system
- update_phone_alias - updates or deletes an existing phone alias record in the system
- add_list - adds a list to the system
- update_list - updates list settings in the system, reset leads in list, delete list
- list_info - summary information about a list
- list_custom_fields - shows the custom fields that are in a list, or all lists
- add_group_alias - adds group alias to the system
- user_group_status - real-time status of one or more user groups
- in_group_status - real-time status of one or more in groups
- agent_status - real-time status of one user
- callid_info - information about a call based upon the caller_code or call ID
- lead_field_info - pulls the value of one field of a lead
- server_refresh - forces a conf file refresh on all telco servers in the cluster
- check_phone_number - allows you to check if a phone number is valid and dialable
- logged_in_agents - list of agents that are logged in to the system
- update_campaign - updates campaign settings in the system
- add_did - adds new Inbound DID entries to the system
- update_did - updates Inbound DID information in the system
- phone_number_log - exports list of calls placed to one of more phone numbers
- ccc_lead_info - outputs lead data for cross-cluster-communication call
- lead_status_search - displays all field values of all leads that match the status and call date in request
- call_status_stats - report on number of calls made by campaign and ingroup, with hourly and status breakdowns
- call_dispo_report - call disposition breakdown report
- lead_callback_info - outputs scheduled callback data for a specific lead
- agent_campaigns - looks up allowed campaigns/in-groups for a specific user
- update_cid_group_entry - updates CID Group entries
- add_dnc_phone - adds a phone number to one of the DNC lists
- add_fpg_phone - adds a phone number to a Filter Phone Group
- campaigns_list - displays information about all campaigns in the system
- hopper_list - displays information about leads in the hopper for a campaign


New scripts:
/vicidial/non_agent_api.php - the script that is accessed to execute commands

API Functions use the 'function' variable

NOTE: Just as with the Agent API, the non-agent API requires the user and pass of a
valid api-enabled vicidial_users account to execute actions.

Example response if user permissions do not allow the function attempted:
ERROR: auth USER DOES NOT HAVE PERMISSION TO USE THIS FUNCTION - 6666|add_lead



--------------------------------------------------------------------------------
version - shows version and build of the API, along with the date/time and timezone

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?function=version

Example responses:
VERSION: 2.4-34|BUILD: 110424-0854|DATE: 2011-05-29 12:19:22|EPOCH: 1306685962|DST: 1|TZ: -5|TZNOW: -4| 





--------------------------------------------------------------------------------
sounds_list - outputs a list of audio files from the audio store

NOTE: api user for this function must have user_level set to 7 or higher

OPTIONAL FIELDS-
format -		format of the output(tab, link, selectframe)
stage -			how to sort the output(date, size, name)
comments -		name of the field to populate


Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=sounds_list&format=selectframe&comments=fieldname&stage=date

Example responses:
<success is inferred by output of audio files list>

ERROR: sounds_list USER DOES NOT HAVE PERMISSION TO VIEW SOUNDS LIST
ERROR: audio store web directory does not exist: |6666|sounds_list|/srv/www/htdocs/1234|
ERROR: sounds_list CENTRAL SOUND CONTROL IS NOT ACTIVE





--------------------------------------------------------------------------------
moh_list - outputs a list of music on hold classes in the system

NOTE: api user for this function must have user_level set to 7 or higher

OPTIONAL FIELDS-
format -		format of the output(only 'selectframe' works)
comments -		name of the field to populate


Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=moh_list&format=selectframe&comments=fieldname&stage=date

Example responses:
<success is inferred by output of moh classes list>

ERROR: moh_list USER DOES NOT HAVE PERMISSION TO VIEW SOUNDS LIST

ERROR: moh_list CENTRAL SOUND CONTROL IS NOT ACTIVE





--------------------------------------------------------------------------------
vm_list - outputs a list of voicemail boxes in the system

NOTE: api user for this function must have user_level set to 7 or higher

OPTIONAL FIELDS-
format -		format of the output(only 'selectframe' works)
comments -		name of the field to populate


Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=vm_list&format=selectframe&comments=fieldname&stage=date

Example responses:
<success is inferred by output of voicemail boxes list>

ERROR: vm_list USER DOES NOT HAVE PERMISSION TO VIEW VOICEMAIL BOXES LIST

ERROR: vm_list CENTRAL SOUND CONTROL IS NOT ACTIVE





--------------------------------------------------------------------------------
blind_monitor - calls user-defined phone and places them in session as blind monitor

NOTE: api user for this function must have user_level set to 7 or higher

REQUIRED FIELDS-
phone_login -		alpha-numeric, no spaces or special characters allowed
session_id -		must be all numbers, 7 digits
server_ip -		must be all numbers and dots, max 15 characters
source -		description of what originated the API call (maximum 20 characters)
stage -			MONITOR, BARGE or HIJACK, default is MONITOR
			 (HIJACK option is not currently functional)


Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=blind_monitor&phone_login=350a&session_id=8600051&server_ip=10.10.10.16&stage=MONITOR

Example responses:
SUCCESS: blind_monitor HAS BEEN LAUNCHED - 350a|010*010*010*017*350|8600051

ERROR: blind_monitor INVALID PHONE LOGIN - 350q 

ERROR: blind_monitor INVALID SESSION ID - 8602051 

ERROR: blind_monitor USER DOES NOT HAVE PERMISSION TO BLIND MONITOR - 6666|0 

ERROR: NO FUNCTION SPECIFIED





--------------------------------------------------------------------------------
agent_ingroup_info - shows in-group and outbound auto-dial info for logged-in agent

NOTE: api user for this function must have user_level set to 7 or higher

REQUIRED FIELDS-
agent_user -		2-20 characters
source -		description of what originated the API call (maximum 20 characters)

SETTINGS FIELDS-
stage -			info(show information only), change(show options to change), text(standard non-HTML output)

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=agent_ingroup_info&stage=change&user=6666&pass=1234&agent_user=1000

Example responses:
ERROR: agent_ingroup_info USER DOES NOT HAVE PERMISSION TO GET AGENT INFO - 6666|0
ERROR: agent_ingroup_info INVALID USER ID - 1255|6666





--------------------------------------------------------------------------------
agent_campaigns - looks up allowed campaigns/in-groups for a specific user

NOTE: api user for this function must have user_level set to 7 or higher

REQUIRED FIELDS-
agent_user -		2-20 characters
source -		description of what originated the API call (maximum 20 characters)

OPTIONAL FIELDS-
campaign_id -		2-8 characters
ignore_agentdirect -	Y or N, default is N. Exclude AGENTDIRECT in-groups from results or not

SETTINGS FIELDS-
stage -			the format of the exported data: csv, tab, pipe(default)
header -		include a header(YES) or not(NO). This is optional, default is not to include a header

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=agent_campaigns&user=6666&pass=1234&agent_user=1000
http://server/vicidial/non_agent_api.php?source=test&function=agent_campaigns&campaign_id=TESTCAMP&ignore_agentdirect=Y&user=6666&pass=1234&agent_user=1000&stage=csv

Example responses:
ERROR: agent_campaigns USER DOES NOT HAVE PERMISSION TO GET AGENT INFO - 6666|0
ERROR: agent_campaigns INVALID USER ID - 1255|6666
ERROR: agent_campaigns AGENT USER DOES NOT EXIST - 1000|6666
ERROR: agent_campaigns THIS AGENT USER HAS NO AVAILABLE CAMPAIGNS - 1000|6666

A SUCCESS response will not show "SUCCESS", but instead will just print the results in the following format:
user|allowed_campaigns_list|allowed_ingroups_list
1000|TESTCAMP-TEST123-TEST987|TEST_IN-TEST_IN2-TEST_IN3-TEST_IN4-SALESLINE





--------------------------------------------------------------------------------
campaigns_list - displays information about all campaigns in the system

NOTE: api user for this function must have user_level set to 7 or higher

REQUIRED FIELDS-
source -		description of what originated the API call (maximum 20 characters)

OPTIONAL FIELDS-
campaign_id -		2-8 characters, for all campaigns, leave blank

SETTINGS FIELDS-
stage -			the format of the exported data: csv, tab, pipe(default)
header -		include a header(YES) or not(NO). This is optional, default is not to include a header

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=campaigns_list&user=6666&pass=1234
http://server/vicidial/non_agent_api.php?source=test&function=campaigns_list&campaign_id=TESTCAMP&user=6666&pass=1234&stage=csv

Example responses:
ERROR: campaigns_list USER DOES NOT HAVE PERMISSION TO GET CAMPAIGN INFO - 6666|0
ERROR: campaigns_list THIS USER HAS NO VIEWABLE CAMPAIGNS - 6666

A SUCCESS response will not show "SUCCESS", but instead will just print the results in the following format:
campaign_id|campaign_name|active|user_group|dial_method|dial_level|lead_order|dial_statuses
TESTCAMP|Test Campaign|Y|---ALL---|INBOUND_MAN|1|DOWN|PDROP DROP B NEW





--------------------------------------------------------------------------------
hopper_list - displays information about leads in the hopper for a campaign

NOTE: api user for this function must have user_level set to 7 or higher

REQUIRED FIELDS-
source -		description of what originated the API call (maximum 20 characters)
campaign_id -		2-8 characters

SETTINGS FIELDS-
stage -			the format of the exported data: csv, tab, pipe(default)
header -		include a header(YES) or not(NO). This is optional, default is not to include a header

OPTIONAL FIELDS-
search_method -		for faster but blocking SQL queries that may affect database performance, set this to BLOCK

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=hopper_list&campaign_id=TESTCAMP&user=6666&pass=1234&stage=csv

Example responses:
ERROR: hopper_list USER DOES NOT HAVE PERMISSION TO GET CAMPAIGN INFO - 6666|0
ERROR: hopper_list THIS CAMPAIGN DOES NOT EXIST - 6666|TESTCAMP
ERROR: hopper_list THERE ARE NO LEADS IN THE HOPPER FOR THIS CAMPAIGN - 6666|TESTCAMP

A SUCCESS response will not show "SUCCESS", but instead will just print the results in the following format:
hopper_order,priority,lead_id,list_id,phone_number,phone_code,state,status,count,gmt_offset,rank,alt,hopper_source,vendor_lead_code,source_id,age_days,last_call_time
0,0,796292,107,9999001095,1,NY,PDROP,7,-4.00,0,,S,1001095,A321,2681,1 YEARS 
1,0,796295,107,9999001098,1,NY,PDROP,2,-4.00,0,,S,1001098,A322,2681,3 YEARS 





--------------------------------------------------------------------------------
recording_lookup - looks up recordings based upon user and date or lead_id

NOTE: api user for this function must have user_level set to 7 or higher and "view reports" enabled

SEARCH FIELDS-
agent_user -		2-20 characters
lead_id -		1-10 digits
date -			date of the calls to pull (must be in YYYY-MM-DD format)
uniqueid -		uniqueid of the call, works best included with another search field
extension -		3-100 characters, the extension listed in the recording_log

SETTINGS FIELDS-
stage -			the format of the exported data: csv, tab, pipe(default)
header -		include a header(YES) or not(NO). This is optional, default is not to include a header
duration -		Y or N, default is N. Includes the duration of the recording in the output(in seconds), before the location

NOTES- 
There is a hard limit of 100000 results

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=recording_lookup&stage=pipe&user=6666&pass=1234&agent_user=1000&date=2010-12-03

Example responses:
ERROR: recording_lookup USER DOES NOT HAVE PERMISSION TO GET RECORDING INFO - 6666|0
ERROR: recording_lookup INVALID SEARCH PARAMETERS - 6666|1000||2010-12-03|
ERROR: recording_lookup NO RECORDINGS FOUND - 1255|6666||2010-12-03|

A SUCCESS response will not show "SUCCESS", but instead will just print the results in the following format:
start_time|user|recording_id|lead_id|location
2010-12-03 12:00:01|1000|534820|876409|http://server/path/to/recording/20101203_120000_1234567890_1000-all.wav

If 'duration' is enabled, this will be the format used:
start_time|user|recording_id|lead_id|duration|location
2010-12-03 12:00:01|1000|534820|876409|63|http://server/path/to/recording/20101203_120000_1234567890_1000-all.wav




--------------------------------------------------------------------------------
did_log_export - exports all calls inbound to a DID for one day

NOTE: api user for this function must have user_level set to 7 or higher and "view reports" enabled

REQUIRED FIELDS-
phone_number -		2-20 characters, the DID that you want to pull logs for
date -			date of the calls to pull (must be in YYYY-MM-DD format)

SETTINGS FIELDS-
stage -			the format of the exported data: csv, tab, pipe(default)
header -		include a header(YES) or not(NO). This is optional, default is not to include a header

NOTES- 
There is a hard limit of 100000 results

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=did_log_export&stage=pipe&user=6666&pass=1234&phone_number=3125551212&date=2010-12-03

Example responses:
ERROR: did_log_export USER DOES NOT HAVE PERMISSION TO GET DID INFO - 6666|0
ERROR: did_log_export INVALID SEARCH PARAMETERS - 6666|3125551212|2010-12-03
ERROR: did_log_export NO RECORDS FOUND - 1255|3125551212|2010-12-03

A SUCCESS response will not show "SUCCESS", but instead will just print the results in the following format:
did_number|call_date|caller_id_number|length_in_sec
3125551212|2010-12-03 12:00:01|7275551212|123





--------------------------------------------------------------------------------
phone_number_log - exports list of calls placed to one of more phone numbers

NOTE: api user for this function must have user_level set to 7 or higher and "view reports" enabled

REQUIRED FIELDS-
phone_number -		the phone number(s) that you want to pull logs for. allows more than one, separated by commas

SETTINGS FIELDS-
stage -			the format of the exported data: csv, tab, pipe(default)
header -		include a header(YES) or not(NO). This is optional, default is not to include a header
detail -		(ALL) calls or only (LAST) call. default is (ALL)
type -			(IN)inbound, (OUT)outbound or (ALL) calls. defauls is (OUT) calls

NOTES- 
There is a hard limit of 100000 results

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=phone_number_log&stage=pipe&user=6666&pass=1234&phone_number=3125551212,9998887112

Example responses:
ERROR: phone_number_log USER DOES NOT HAVE PERMISSION TO GET CALL LOG INFO - 6666|0
ERROR: phone_number_log NO VALID PHONE NUMBERS DEFINED - 6666|312|2010-12-03
ERROR: phone_number_log NO RECORDS FOUND - 1255|3125551212|2010-12-03

A SUCCESS response will not show "SUCCESS", but instead will just print the results in the following format:
phone_number|call_date|list_id|length_in_sec|lead_status|hangup_reason|call_status|source_id|user
9998887112|2017-01-06 08:11:01|82106|25|TD|AGENT|SALE|mail098|6666





--------------------------------------------------------------------------------
agent_stats_export - exports agent activity statistics

NOTE: api user for this function must have user_level set to 7 or higher and "view reports" enabled

REQUIRED FIELDS-
source -		description of what originated the API call (maximum 20 characters)
datetime_start -	start date/time of the agent activity to pull (must be in "YYYY-MM-DD+HH:MM:SS" format)
datetime_end -		end date/time of the agent activity to pull (must be in "YYYY-MM-DD+HH:MM:SS" format)

SETTINGS FIELDS-
agent_user -		2-20 characters, use only for one agent stats <optional>
campaign_id -		2-8 characters, use only for one campaign stats <optional>
stage -			the format of the exported data: csv, tab, pipe(default)
header -		include a header(YES) or not(NO). This is optional, default is not to include a header
time_format -		time format('H','HF','M','S') in hours, minutes or seconds: H = 1:23:45, M = 83:45, S = 5023  (default 'HF')
				* HF will force hour format even for zero seconds time "0:00:00"
group_by_campaign -	divide and output the agent results grouped by campaign(YES) or not(NO). This is optional, default is 'NO'

NOTES- 
There is a hard limit of 10000000 records analyzed

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=agent_stats_export&time_format=M&stage=pipe&user=6666&pass=1234&agent_user=6666&datetime_start=2012-08-09+00:00:00&datetime_end=2012-08-10+23:59:59
http://server/vicidial/non_agent_apiX.php?DB=0&stage=pipe&user=6666&pass=1234&source=test&function=agent_stats_export&datetime_start=2012-03-26+00:00:00&datetime_end=2012-03-26+23:59:59&header=YES

Example responses:
ERROR: agent_stats_export USER DOES NOT HAVE PERMISSION TO GET AGENT INFO - 6666|0
ERROR: agent_stats_export INVALID SEARCH PARAMETERS - 6666||2010-12-03 00:00:00|2010-12-03 00:00:01|
ERROR: agent_stats_export NO RECORDS FOUND - 1255||2010-12-03 00:00:00|2010-12-03 00:00:01|

A SUCCESS response will not show "SUCCESS", but instead will just print the results in the following format:
user|full_name|user_group|calls|login_time|total_talk_time|avg_talk_time|avg_wait_time|pct_of_queue|pause_time|sessions|avg_session|pauses|avg_pause_time|pause_pct|pauses_per_session|total_wait_time
1029|test agent|ADMIN|1|0:01:52|0:00:16|0:00:16|0:00:18|16.7%|54|3|0:00:37|5|0:00:11|48.2%|2|0:00:32
6666|Admin|ADMIN|5|0:04:11|0:01:48|0:00:22|0:00:17|83.3%|27|2|0:02:06|4|0:00:07|10.8%|2|0:00:32

* if the 'group_by_campaign' option is enabled, the format above will have 'campaign_id' as the first column, and each agent's activity will be divided on separate lines by the campaigns they were logged into during the requested time period.





--------------------------------------------------------------------------------
user_group_status - real-time status of one or more user groups

NOTE: api user for this function must have user_level set to 7 or higher and "view reports" enabled

REQUIRED FIELDS-
source -		description of what originated the API call (maximum 20 characters)
user_groups -		pipe-delimited list of user groups to get status information for "ADMIN|AGENTS"

SETTINGS FIELDS-
stage -			the format of the exported data: csv, tab, pipe(default)
header -		include a header(YES) or not(NO). This is optional, default is not to include a header

NOTES- 
There is a hard limit of 10000000 records analyzed

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=user_group_status&user_groups=ADMIN|AGENT&header=YES

Example responses:
ERROR: user_group_status USER DOES NOT HAVE PERMISSION TO GET USER GROUP INFO - 6666|0
ERROR: user_group_status INVALID SEARCH PARAMETERS - 6666||

A SUCCESS response will not show "SUCCESS", but instead will just print the results in the following format:
usergroups|calls_waiting|agents_logged_in|agents_in_calls|agents_waiting|agents_paused|agents_in_dead_calls|agents_in_dispo|agents_in_dial
ADMIN AGENT|0|1|0|1|0|0|0|0





--------------------------------------------------------------------------------
in_group_status - real-time status of one or more in groups

NOTE: api user for this function must have user_level set to 7 or higher and "view reports" enabled

REQUIRED FIELDS-
source -		description of what originated the API call (maximum 20 characters)
in_groups -		pipe-delimited list of inbound groups to get status information for "SALESLINE|SUPPORT"

SETTINGS FIELDS-
stage -			the format of the exported data: csv, tab, pipe(default)
header -		include a header(YES) or not(NO). This is optional, default is not to include a header

NOTES- 
There is a hard limit of 10000000 records analyzed

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&stage=csv&function=in_group_status&in_groups=SALESLINE|SUPPORT&header=YES

Example responses:
ERROR: in_group_status USER DOES NOT HAVE PERMISSION TO GET IN-GROUP INFO - 6666|0
ERROR: in_group_status INVALID SEARCH PARAMETERS - 6666||

A SUCCESS response will not show "SUCCESS", but instead will just print the results in the following format:
ingroups,total_calls,calls_waiting,agents_logged_in,agents_in_calls,agents_waiting,agents_paused,agents_in_dispo,agents_in_dial
SALESLINE SUPPORT,31,0,75,54,0,20,1,0




--------------------------------------------------------------------------------
agent_status - real-time status of one agent user

NOTE: api user for this function must have user_level set to 7 or higher and "view reports" enabled

REQUIRED FIELDS-
source -		description of what originated the API call (maximum 20 characters)
agent_user -		2-20 characters, use only for one agent status

SETTINGS FIELDS-
stage -			the format of the exported data: csv, tab, pipe(default)
header -		include a header(YES) or not(NO). This is optional, default is not to include a header

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=agent_status&agent_user=1234&stage=csv&header=YES

Example responses:
ERROR: agent_status USER DOES NOT HAVE PERMISSION TO GET AGENT INFO - 6666|0
ERROR: agent_status INVALID SEARCH PARAMETERS - 6666||
ERROR: agent_status AGENT NOT FOUND - 6666||
ERROR: agent_status AGENT NOT LOGGED IN - 6666||

A SUCCESS response will not show "SUCCESS", but instead will just print the results in the following format:
status,call_id,lead_id,campaign_id,calls_today,full_name,user_group,user_level,pause_code,real_time_sub_status,phone_number,vendor_lead_code,session_id
INCALL,M4050908070000012345,12345,TESTCAMP,1,Test Agent,AGENTS,3,LOGIN,,7275551212,123456,8600051
INCALL|M4181606420000000104|104|TESTBLND|1|Admin|ADMIN|9|BRK2|DEAD|3125551212|123457|8600051
PAUSED||105|TESTBLND|1|Admin|ADMIN|9||PREVIEW|9545551212|123458|8600052

NOTE: real_time_sub_status field can consist of: DEAD, DISPO, 3-WAY, PARK, RING, PREVIEW, DIAL or it can be empty




--------------------------------------------------------------------------------
callid_info - information about a call based upon the caller_code or call ID

NOTE: api user for this function must have user_level set to 7 or higher and "view reports" enabled

REQUIRED FIELDS-
source -		description of what originated the API call (maximum 20 characters)
call_id -		16-40 characters

SETTINGS FIELDS-
stage -			the format of the exported data: csv, tab, pipe(default)
header -		include a header(YES) or not(NO). This is optional, default is not to include a header
detail -		if set to YES, more call info will be output. Default is NO, only callid and customer talk time will be output

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=callid_info&call_id=M4050908070000012345&stage=csv&header=YES

Example responses:
ERROR: callid_info USER DOES NOT HAVE PERMISSION TO GET CALL INFO - 6666|0
ERROR: callid_info INVALID SEARCH PARAMETERS - 6666||
ERROR: callid_info CALL NOT FOUND - 6666||
ERROR: callid_info CALL LOG NOT FOUND - 6666||

A SUCCESS response will not show "SUCCESS", but instead will just print the results in the following format:
call_id,custtime
M4050908070000012345,725

Setting "detail=YES" will result in the following output:
call_id,custtime,call_date,campaign_id,list_id,status,user,phone
M4050908070000012345,725,2014-01-24 09:26:09,TESTCAMP,999,A,6666,3125551212




--------------------------------------------------------------------------------
lead_field_info - pulls the value of one field of a lead

NOTE: api user for this function must have user_level set to 7 or higher and "modify leads" enabled

REQUIRED FIELDS-
source -		description of what originated the API call (maximum 20 characters)
lead_id -		1-10 digits
field_name -		name of lead field to pull

OPTIONAL FIELDS-
custom_fields -		Y or N, default is N. If the field you want to pull is a custom field or not
list_id -		override for the entry_list_id of a custom field

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=lead_field_info&field_name=cc_value&lead_id=1139739&custom_fields=Y&list_id=999888999777

Example responses:
ERROR: lead_field_info USER DOES NOT HAVE PERMISSION TO GET LEAD INFO - 6666|0
ERROR: lead_field_info INVALID SEARCH PARAMETERS - 6666||
ERROR: lead_field_info LIST NOT FOUND - 6666||
ERROR: lead_field_info LEAD NOT FOUND - 6666||
ERROR: lead_field_info LEAD FIELD NOT FOUND - 6666||

A SUCCESS response will not show "SUCCESS", but instead will just print the value of the field:
1234567890





--------------------------------------------------------------------------------
lead_status_search - displays all field values of all leads that match the status and call date in request

NOTE: api user for this function must have user_level set to 7 or higher and "modify leads" enabled

REQUIRED FIELDS-
source -		description of what originated the API call (maximum 20 characters)
status -		status of leads to be pulled
date -			called date of the leads to pull (must be in YYYY-MM-DD format)
			NOTE: both inbound and outbound call logs are searched, no more than 2000 responses are displayed

OPTIONAL FIELDS-
lead_id -		1-10 digits, only used if status and date are empty 
custom_fields -		Y or N, default is N. Display custom fields values or not
list_id -		override for the entry_list_id of a custom field

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=lead_status_search&status=NP&date=2017-12-29&custom_fields=Y

Example responses:
ERROR: lead_status_search USER DOES NOT HAVE PERMISSION TO GET LEAD INFO - 6666|0
ERROR: lead_status_search INVALID SEARCH PARAMETERS - 6666|||
ERROR: lead_status_search NO RESULTS FOUND - 6666|||

A SUCCESS response will not show "SUCCESS", but instead will print the values of all lead fields, one per line, with leads beginning with ";---------- START OF RECORD 1 ----------":

;---------- START OF RECORD 1 ----------
lead_id => 823602
status => TD
user => 6666
vendor_lead_code => 1028405
source_id => I
list_id => 107
gmt_offset_now => -5.00
phone_code => 1
phone_number => 9999028405
...
test_custom_field => new value





--------------------------------------------------------------------------------
ccc_lead_info - outputs lead data for cross-cluster-communication call

NOTE: api user for this function must have user_level set to 7 or higher and "view reports" enabled

REQUIRED FIELDS-
source -		description of what originated the API call (maximum 20 characters)
call_id -		16-40 characters

SETTINGS FIELDS-
stage -			the format of the exported data: csv, tab, newline, pipe(default)
header -		include a header(YES) or not(NO). This is optional, default is not to include a header

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=ccc_lead_info&call_id=M4050908070000012345&stage=csv&header=YES

Example responses:
ERROR: ccc_lead_info USER DOES NOT HAVE PERMISSION TO GET LEAD INFO - 6666|0
ERROR: ccc_lead_info INVALID SEARCH PARAMETERS - 6666||
ERROR: ccc_lead_info CALL NOT FOUND - 6666||
ERROR: ccc_lead_info LEAD NOT FOUND - 6666||

A SUCCESS response will not show "SUCCESS", but instead will just print the results in the following format:
status|user|vendor_lead_code|source_id|list_id|gmt_offset_now|phone_code|phone_number|title|first_name|middle_initial|last_name|address1|address2|address3|city|state|province|postal_code|country_code|gender|date_of_birth|alt_phone|email|security_phrase|comments|called_count|last_local_call_time|rank|owner
TD|6666|554443||501|-4.00|1|9998887112|Mr|Billy'Oj|U|O'Reillyo|ad1|ad2|ad3|city next|FL|U|33777||U|0000-00-00|bobo'brien|test@test.com||2017-06-01 18:38:41|79|2017-06-09 14:53:27|0|4646





--------------------------------------------------------------------------------
lead_callback_info - outputs scheduled callback data for a specific lead

NOTE: api user for this function must have user_level set to 7 or higher and "view reports" enabled

REQUIRED FIELDS-
source -		description of what originated the API call (maximum 20 characters)
lead_id -		16-40 characters

SETTINGS FIELDS-
stage -			the format of the exported data: csv, tab, newline, pipe(default)
header -		include a header(YES) or not(NO). This is optional, default is not to include a header
search_location -	Where to check for scheduled callback records in the system, can select only one(default is CURRENT):
				CURRENT - check for active scheduled callbacks
				ARCHIVE - check for archived scheduled callbacks
				ALL - check for both active and archived scheduled callbacks

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=lead_callback_info&lead_id=12345&stage=pipe&header=YES&search_location=ALL
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=lead_callback_info&lead_id=846446&search_location=ALL&header=YES

Example responses:
ERROR: lead_callback_info USER DOES NOT HAVE PERMISSION TO GET LEAD INFO - 6666|0
ERROR: lead_callback_info INVALID SEARCH PARAMETERS - 6666||
ERROR: lead_callback_info LEAD NOT FOUND - 6666||
ERROR: lead_callback_info CALLBACK NOT FOUND - 6666||

A SUCCESS response will not show "SUCCESS", but instead will just print the results in the following format(The most recent callback entry will be at the bottom of the results):
lead_id|callback_type|recipient|callback_status|lead_status|callback_date|entry_date|user|list_id|campaign_id|user_group|customer_timezone|customer_time|comments
846446|ARCHIVE|USERONLY|INACTIVE|CBTEST|2018-09-01 15:00:00|2018-08-27 01:06:45|6666|107|TESTCAMP|ADMIN|AUS-ACST-N-Northern Territory Time Zone|2018-08-27 00:00:00|
846446|ARCHIVE|USERONLY|INACTIVE|CBTEST|2018-12-14 14:00:00|2018-12-14 16:42:32|6666|107|TESTCAMP|ADMIN||2018-12-14 14:00:00|Call back after lunch
846446|CURRENT|USERONLY|INACTIVE|CBTEST|2018-12-14 19:00:00|2018-12-14 16:44:08|6666|107|TESTCAMP|ADMIN||2018-12-14 19:00:00|Call back after dinner
846446|CURRENT|USERONLY|ACTIVE|CBLEAD|2018-12-17 10:00:00|2018-12-14 16:50:56|6666|107|TESTCAMP|ADMIN|USA-EST-Y-Eastern US Time Zone|2018-12-17 10:00:00|try back Monday morning





--------------------------------------------------------------------------------
update_log_entry - updates the status of a vicidial_log or vicidial_closer_log entry

NOTE: api user for this function must have user_level set to 8 or higher and "modify leads" enabled

REQUIRED FIELDS-
source -		description of what originated the API call (maximum 20 characters)
call_id -		either the uniqueid or the 20-character ID of the call
group -			either a campaign_id or an in-group group_id
status -		the new status for the log record to be set to

NOTES- 
There is a hard limit of 10000000 records analyzed

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_log_entry&group=SALESLINE&call_id=Y2010309520000000234&status=SALEX

Example responses:
ERROR: update_log_entry USER DOES NOT HAVE PERMISSION TO UPDATE LOGS - 6666|0
ERROR: update_log_entry INVALID SEARCH PARAMETERS - 6666||
ERROR: update_log_entry GROUP NOT FOUND - 6666||
ERROR: update_log_entry NO RECORDS FOUND - 6666||||
ERROR: update_log_entry NO RECORDS UPDATED - 6666|TESTCAMP|M9131554060000426264|SALEX|SALEX|1347566056.399
SUCCESS: update_log_entry RECORD HAS BEEN UPDATED - 6666|6666|TESTCAMP|M9131554060000426264|SALEX|WN|1347566056.399|





--------------------------------------------------------------------------------
add_lead - adds a new lead to the vicidial_list table with several fields and options

NOTE: api user for this function must have modify_leads set to 1 and user_level
      must be set to 8 or higher

REQUIRED FIELDS-
phone_number -		must be all numbers, 6-16 digits
phone_code -		must be all numbers, 1-4 digits, defaults to 1 if not set
list_id -		must be all numbers, 3-12 digits, defaults to 999 if not set
source -		description of what originated the API call (maximum 20 characters)

SETTINGS FIELDS-
dnc_check -		Y, N or AREACODE, default is N
campaign_dnc_check -	Y, N or AREACODE, default is N
campaign_id -		2-8 Character campaign ID, required if using campaign_dnc_check or callbacks
add_to_hopper -		Y or N, default is N
hopper_priority -	99 to -99, the higher number the higher priority, default is 0
hopper_local_call_time_check - Y or N, default is N. Validate the local call time and/or state call time before inserting lead in the hopper
duplicate_check -	Check for duplicate records in the system, can select more than one (duplicate_check=DUPLIST-DUPTITLEALTPHONELIST)
			If duplicate is found, will return error, the duplicate data and lead_id and list_id of existing record
			Here are the duplicate_check options:
				DUPLIST - check for duplicate phone_number in same list
				DUPCAMP - check for duplicate phone_number in all lists for this list's campaign
				DUPSYS - check for duplicate phone_number in entire system
				DUPTITLEALTPHONELIST - check for duplicate title and alt_phone in same list
				DUPTITLEALTPHONECAMP - check for duplicate title and alt_phone in all lists for this list's campaign
				DUPTITLEALTPHONESYS - check for duplicate title and alt_phone in entire system
				DUPNAMEPHONELIST - check for duplicate first_name, last_name and phone_number in same list
				DUPNAMEPHONECAMP - check for duplicate first_name, last_name and phone_number in all lists for this list's campaign
				DUPNAMEPHONESYS - check for duplicate first_name, last_name and phone_number in entire system
				 "  30/60/90/180/360DAY - Added to one of the above duplicate checks(i.e. "DUPSYS90DAY"), only checks leads loaded in last 90 days
usacan_prefix_check -	Y or N, default is N. Check for a valid 4th digit for USA and Canada phone numbers (cannot be 0 or 1)
usacan_areacode_check -	Y or N, default is N. Check for a valid areacode for USA and Canada phone numbers(also checks for 10-digit length)
nanpa_ac_prefix_check -	Y or N, default is N. Check for a valid NANPA areacode and prefix, if optional NANPA data is on the system
custom_fields -		Y or N, default is N. Defines whether the API will accept custom field data when inserting leads into the vicidial_list table
			For custom fields to be inserted, just add the field label as a variable to the URL string
			For example, if the field_label is "favorite_color" you would add "&favorite_color=blue"
tz_method -		<empty>, POSTAL, TZCODE or NANPA, default is <empty> which will use the country code and areacode for time zone lookups
				POSTAL relies on the postal_code field
				TZCODE relies on the owner field being populated with a proper time zone code
				NANPA relies on the optional NANPA areacode prefix data being loaded on your system
callback -		Y or N, default is N. Set this lead as a scheduled callback. campaign_id field is REQUIRED for callbacks
callback_status -	1-6 Character, callback status to use, default is CALLBK (vicidial_list status will be set to CBHOLD to lock)
callback_datetime -	YYYY-MM-DD+HH:MM:SS, date and time of scheduled callback. REQUIRED if callback is set. NOW can be used for current datetime.
callback_type -		USERONLY or ANYONE, default is ANYONE
callback_user -		User ID the USERONLY callback is assigned to
callback_comments -	Optional comments to appear when the callback is called back
lookup_state -		Y or N, default is N. Looks up state field from areacode list. Only works if the 'state' field is not populated.
list_exists_check -	Y or N, default is N. If the list_id is not a defined list in the system, it will ERROR and not insert the lead.

(for fields with spaces in the values, you can replace the space with a plus + sign[address, city, first_name, etc...])
OPTIONAL FIELDS- 
vendor_lead_code -	1-20 characters
source_id  -		1-50 characters
gmt_offset_now -	overridden by auto-lookup of phone_code and area_code portion of phone number if applicable
title -			1-4 characters
first_name -		1-30 characters
middle_initial -	1 character
last_name -		1-30 characters
address1 -		1-100 characters
address2 -		1-100 characters
address3 -		1-100 characters
city -			1-50 characters
state -			2 characters
province -		1-50 characters
postal_code -		1-10 characters
country_code -		3 characters
gender -		U, M, F (Undefined, Male, Female) - defaults to 'U'
date_of_birth -		YYYY-MM-DD
alt_phone -		1-12 characters
email -			1-70 characters
security_phrase -	1-100 characters
comments -		1-255 characters
multi_alt_phones -	5-1024 characters (see examples for more info)
rank -			1-5 digits
owner -			1-20 characters (user ID, Territory or user group)
entry_list_id -		WARNING! ONLY USE IF YOU KNOW WHAT YOU ARE DOING, CAN BREAK CUSTOM FIELDS! (must be all numbers, 3-12 digits, will not work if custom_fields is set to Y)


Multi-ALT-Phones format:

7275551212_1_work!7275551213_1_sister+house!1234567890_1_neighbor

The multi-alt-phones field is formatted as a field of phone-number/phone-code/phone-note set of data(phone code and alt_note are both optional and the phone code can be overridden by the force phone code flag). The record delimiter is an exclamation point with the optional phone code and note delimited within the record by an underscore character _.


Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=add_lead&phone_number=7275551111

http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=add_lead&phone_number=7275551212&phone_code=1&list_id=999&dnc_check=N&first_name=Bob&last_name=Wilson

http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=add_lead&phone_number=7275551111&phone_code=1&list_id=999&dnc_check=N&first_name=Bob&last_name=Wilson&add_to_hopper=Y&hopper_local_call_time_check=Y

http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=add_lead&phone_number=7275551111&phone_code=1&list_id=999&dnc_check=N&campaign_dnc_check=Y&campaign_id=TESTCAMP&first_name=Bob&last_name=Wilson&address1=1234+Main+St.&city=Chicago+Heights&state=IL&add_to_hopper=Y&hopper_local_call_time_check=Y&multi_alt_phones=7275551212_1_work!7275551213_1_sister+house!1234567890_1_neighbor

http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=add_lead&phone_number=7275551212&phone_code=1&list_id=999&dnc_check=N&first_name=Bob&last_name=Wilson&duplicate_check=DUPLIST-DUPNAMEPHONELIST

http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=add_lead&phone_number=7275551212&phone_code=1&list_id=999&custom_fields=Y&favorite_color=blue

http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=add_lead&phone_number=7275551111&campaign_id=TESTCAMP&callback=Y&callback_status=CALLBK&callback_datetime=NOW&callback_type=USERONLY&callback_user=6666&callback_comments=Comments+go+here

Example responses: (The "data" values for a successful add_lead request are: phone_number, list_id, lead_id, gmt_offset_now)
SUCCESS: add_lead LEAD HAS BEEN ADDED - 7275551111|6666|999|193715|-4
NOTICE: add_lead ADDED TO HOPPER - 7275551111|6666|193715|1677922

SUCCESS: add_lead LEAD HAS BEEN ADDED - 7275551111|6666|999|193716|-4
NOTICE: add_lead CUSTOM FIELDS VALUES ADDED - 7275551111|1234|101
NOTICE: add_lead CUSTOM FIELDS NOT ADDED, CUSTOM FIELDS DISABLED - 7275551111|Y|0
NOTICE: add_lead CUSTOM FIELDS NOT ADDED, NO CUSTOM FIELDS DEFINED FOR THIS LIST - 7275551111|1234|101
NOTICE: add_lead CUSTOM FIELDS NOT ADDED, NO FIELDS DEFINED - 7275551111|1234|101
NOTICE: add_lead MULTI-ALT-PHONE NUMBERS LOADED - 3|6666|193716
NOTICE: add_lead NOT ADDED TO HOPPER, OUTSIDE OF LOCAL TIME - 7275551111|6666|193716|-4|0

NOTICE: add_lead SCHEDULED CALLBACK ADDED - 1234|2011-09-29 12:00:01|TESTCAMP|6666|USERONLY|CALLBK
NOTICE: add_lead SCHEDULED CALLBACK NOT ADDED, USER NOT VALID - 1234|TESTCAMP|6|
NOTICE: add_lead SCHEDULED CALLBACK NOT ADDED, CAMPAIGN NOT VALID - 1234|XYZ

NOTICE: add_lead NANPA options disabled, NANPA prefix data not loaded - 0|6666

ERROR: add_lead INVALID PHONE NUMBER LENGTH - 72755|6666 
ERROR: add_lead INVALID PHONE NUMBER PREFIX - 72755|6666 
ERROR: add_lead INVALID PHONE NUMBER AREACODE - 72755|6666 
ERROR: add_lead INVALID PHONE NUMBER NANPA AREACODE PREFIX - 7275551212|6666

ERROR: add_lead USER DOES NOT HAVE PERMISSION TO ADD LEADS TO THE SYSTEM - 6666|0 
ERROR: add_lead NOT AN ALLOWED LIST ID - 7275551212|98762
ERROR: add_lead NOT A DEFINED LIST ID, LIST EXISTS CHECK ENABLED - 7275551212|12344

ERROR: NO FUNCTION SPECIFIED

ERROR: add_lead DUPLICATE PHONE NUMBER IN LIST - 7275551111|101|8765444
ERROR: add_lead DUPLICATE PHONE NUMBER IN CAMPAIGN LISTS - 7275551111|101|8765444|101
ERROR: add_lead DUPLICATE PHONE NUMBER IN SYSTEM - 7275551111|101|8765444|101
ERROR: add_lead DUPLICATE TITLE ALT_PHONE IN LIST - 1234|7275551111|101|8765444
ERROR: add_lead DUPLICATE TITLE ALT_PHONE IN CAMPAIGN LISTS - 1234|7275551111|101|8765444|101
ERROR: add_lead DUPLICATE TITLE ALT_PHONE IN SYSTEM - 1234|7275551111|101|8765444|101
ERROR: add_lead DUPLICATE NAME PHONE IN LIST - Bob|Smith|7275551113|101|8765444|101
ERROR: add_lead DUPLICATE NAME PHONE IN CAMPAIGN LISTS - Bob|Smith|7275551113|101|8765444|101
ERROR: add_lead DUPLICATE NAME PHONE IN SYSTEM - Bob|Smith|7275551113|101|8765444|101





--------------------------------------------------------------------------------
update_lead - updates lead information in the vicidial_list and custom_ tables

NOTE: api user for this function must have modify_leads set to 1 and user_level
      must be set to 8 or higher

REQUIRED FIELDS-
lead_id -		must be all numbers, 1-9 digits, not required if using vendor_lead_code or phone_number
vendor_lead_code -	can be used instead of lead_id to match leads
phone_number -		can be used instead of lead_id or vendor_lead_code to match leads
source -		description of what originated the API call (maximum 20 characters)

SETTINGS FIELDS-
search_method -		You can combine the following 3 options in this field to search the parameters you desire:
				LEAD_ID, will attempt to find a match with the lead_id
				VENDOR_LEAD_CODE, will attempt to find a match with the vendor_lead_code
				PHONE_NUMBER, will attempt to find a match with the phone_number
			  For example to search lead_id and vendor_lead_code: "&search_method=LEAD_ID_VENDOR_LEAD_CODE"
			  The search order is NOT preserved, Lead ID is always first, Vendor Lead Code is second
			  and Phone number is last. Default is "LEAD_ID"
search_location -	Where to check for records in the system, can select only one(default is SYSTEM):
				LIST - check for lead in same list
				CAMPAIGN - check for lead in all lists for this list's campaign
				SYSTEM - check for lead in entire system
			  If no list_id is defined, the the search_location will be assumed as SYSTEM
insert_if_not_found -	Y or N, will attempt to insert as a NEW lead if no match is found, default is N.
			Insertion will require phone_code, phone_number and list_id. lead_id will be ignored.
			Most of the add_lead options that are not available if you use this setting in this function
records -		number of records to update if more than 1 found (defaults to '1'[most recently loaded lead])
custom_fields -		Y or N, default is N. Defines whether the API will accept custom field data when updating leads in the vicidial_list table
			For custom fields to be updated, just add the field label as a variable to the URL string
			For example, if the field_label is "favorite_color" you would add "&favorite_color=blue"
no_update -		Y or N, Setting this to Y will not perform any updates, but will instead only tell
			you if a lead exists that matches the search criteria, default is N.
delete_lead -		Y or N, Setting this to Y will delete the lead from the vicidial_list table, default is N.
reset_lead -		Y or N, Setting this to Y will reset the called-since-last-reset flag of the lead, default is N.
callback -		Y, N or REMOVE, default is N. Set this lead as a scheduled callback. REMOVE will delete the scheduled callback entry
callback_status -	1-6 Character, callback status to use, default is CALLBK (vicidial_list status will be set to CBHOLD to lock)
callback_datetime -	YYYY-MM-DD+HH:MM:SS, date and time of scheduled callback. REQUIRED if callback is set. NOW can be used for current datetime.
callback_type -		USERONLY or ANYONE, default is ANYONE
callback_user -		User ID the USERONLY callback is assigned to
callback_comments -	Optional comments to appear when the callback is called back
update_phone_number -	Y or N, Optional setting to update the phone_number field, default is N.
add_to_hopper -		Y or N, default is N
remove_from_hopper -	Y or N, default is N
hopper_priority -	99 to -99, the higher number the higher priority, default is 0
hopper_local_call_time_check - Y or N, default is N. Validate the local call time and/or state call time before inserting lead in the hopper
list_exists_check -	Y or N, default is N. If the list_id_field is not a defined list in the system, it will ERROR and not update the lead.

EDITABLE FIELDS- 
user_field -		1-20 characters, this updates the 'user' field in the vicidial_list table
list_id_field -		3-12 digits, this updates the 'list_id' field in the vicidial_list table
status -		1-6 characters, not punctuation or spaces
vendor_lead_code -	1-20 characters
source_id  -		1-50 characters
gmt_offset_now -	overridden by auto-lookup of phone_code and area_code portion of phone number if applicable
title -			1-4 characters
first_name -		1-30 characters
middle_initial -	1 character
last_name -		1-30 characters
address1 -		1-100 characters
address2 -		1-100 characters
address3 -		1-100 characters
city -			1-50 characters
state -			2 characters
province -		1-50 characters
postal_code -		1-10 characters
country_code -		3 characters
gender -		U, M, F (Undefined, Male, Female) - defaults to 'U'
date_of_birth -		YYYY-MM-DD
alt_phone -		1-12 characters
email -			1-70 characters
security_phrase -	1-100 characters
comments -		1-255 characters
rank -			1-5 digits
owner -			1-20 characters (user ID, Territory or user group)
called_count -		digits only, the number of attempts dialing the lead
entry_list_id -		WARNING! ONLY USE IF YOU KNOW WHAT YOU ARE DOING, CAN BREAK CUSTOM FIELDS! (must be all numbers, 3-12 digits, will not work if custom_fields is set to Y)
force_entry_list_id -	WARNING! ONLY USE IF YOU KNOW WHAT YOU ARE DOING, CAN BREAK CUSTOM FIELDS! (must be all numbers, 3-12 digits, will override entry_list_id to this value in all custom fields queries executed by this command)
NOTES: 
 - in order to set a field to empty('') set it equal to --BLANK--, i.e. "&province=--BLANK--"
 - please use no special characters like apostrophes, double-quotes or amphersands

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_lead&lead_id=27&last_name=SMITH

http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_lead&search_method=VENDOR_LEAD_CODE&vendor_lead_code=1000019&last_name=JOHNSON

http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_lead&search_method=PHONE_NUMBER&records=2&list_id=8107&search_location=LIST&phone_number=9999000019&last_name=WILSON

http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_lead&lead_id=405794&last_name=SMITH&city=Chicago&custom_fields=Y&favorite_color=blue

http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_lead&search_location=LIST&search_method=PHONE_NUMBER&insert_if_not_found=Y&phone_number=9999000029&phone_code=1&list_id=999&first_name=Bob&last_name=Wilson&city=Chicago&custom_fields=Y&favorite_color=red

http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_lead&insert_if_not_found=Y&search_method=VENDOR_LEAD_CODE_PHONE_NUMBER&vendor_lead_code=89763545&phone_number=7275551212&phone_code=1&list_id=999&first_name=Bob&last_name=Wilson&custom_fields=Y&favorite_color=blue

http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_lead&search_location=LIST&search_method=VENDOR_LEAD_CODE_PHONE_NUMBER&insert_if_not_found=Y&phone_number=9999000029&phone_code=1&list_id=999&first_name=Bob&last_name=Wilson&city=Chicago&custom_fields=Y&favorite_color=red&user_field=1008&list_id_field=107&status=OLD

http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_lead&lead_id=27&no_update=Y

http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_lead&lead_id=27&delete_lead=Y

http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_lead&lead_id=27&delete_lead=Y&custom_fields=Y

http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_lead&lead_id=406757&campaign_id=TESTCAMP&callback=Y&callback_status=CALLBK&callback_datetime=NOW&callback_type=USERONLY&callback_user=1028&callback_comments=Comments+go+here+again

http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_lead&search_location=SYSTEM&search_method=PHONE_NUMBER&phone_number=9998887112&no_update=Y&add_to_hopper=Y&hopper_priority=99&hopper_local_call_time_check=Y

Example responses:
SUCCESS: update_lead LEAD HAS BEEN UPDATED - 6666|193716
NOTICE: update_lead CUSTOM FIELDS VALUES UPDATED - 7275551111|1234|101
NOTICE: update_lead CUSTOM FIELDS NOT UPDATED, CUSTOM FIELDS DISABLED - 7275551111|Y|0
NOTICE: update_lead CUSTOM FIELDS NOT UPDATED, NO CUSTOM FIELDS DEFINED FOR THIS LIST - 7275551111|1234|101
NOTICE: update_lead CUSTOM FIELDS NOT UPDATED, NO FIELDS DEFINED - 7275551111|1234|101

NOTICE: update_lead SCHEDULED CALLBACK UPDATED - 1234|2011-09-29 12:00:01|TESTCAMP|6666|USERONLY|CALLBK
NOTICE: update_lead SCHEDULED CALLBACK NOT UPDATED, NO FIELDS SPECIFIED - 1234|
NOTICE: update_lead SCHEDULED CALLBACK ADDED - 1234|2011-09-29 12:00:01|TESTCAMP|6666|USERONLY|CALLBK
NOTICE: update_lead SCHEDULED CALLBACK NOT ADDED, USER NOT VALID - 1234|TESTCAMP|6|
NOTICE: update_lead SCHEDULED CALLBACK NOT ADDED, CAMPAIGN NOT VALID - 1234|XYZ

NOTICE: update_lead NO MATCHES FOUND IN THE SYSTEM - 6666|4567|897654327|7275551212
SUCCESS: update_lead LEAD HAS BEEN ADDED - 7275551111|6666|999|193716|-4

SUCCESS: update_lead LEAD HAS BEEN DELETED - 7275551111|6666|999|193716|-4
NOTICE: update_lead CUSTOM FIELDS ENTRY DELETED 1 - 7275551111|6666|999|193716|-4

NOTICE: update_lead LEADS FOUND IN THE SYSTEM: |6666|1010542|12345678901234|9998887112|3333444|0
					       (user|lead_id|vendorleadcode|phone     |list_id|entry_list_id)

NOTICE: update_lead ADDED TO HOPPER - 7275551111|6666|193715|1677922
NOTICE: update_lead NOT ADDED TO HOPPER - 7275551111|6666|193715|1677922
NOTICE: update_lead NOT ADDED TO HOPPER, OUTSIDE OF LOCAL TIME - 7275551111|193715|-5|0|6666
NOTICE: update_lead NOT ADDED TO HOPPER, LEAD NOT FOUND - 7275551111|193715|6666
NOTICE: update_lead NOT ADDED TO HOPPER, LEAD IS ALREADY IN THE HOPPER - 7275551111|193715|6666
NOTICE: update_lead REMOVED FROM HOPPER - 7275551111|193715|READY|6666
NOTICE: update_lead NOT REMOVED FROM HOPPER - 7275551111|193715|DNC|6666
NOTICE: update_lead NOT REMOVED FROM HOPPER, LEAD IS NOT IN THE HOPPER - 7275551111|193715|6666

ERROR: update_lead INVALID DATA FOR LEAD INSERTION - 6666|||
ERROR: update_lead NO MATCHES FOUND IN THE SYSTEM - 6666|||
ERROR: update_lead NO VALID SEARCH METHOD - 6666|SYSTEM|||
ERROR: update_lead NOT A DEFINED LIST ID, LIST EXISTS CHECK ENABLED - 6666|139

ERROR: update_lead USER DOES NOT HAVE PERMISSION TO UPDATE LEADS IN THE SYSTEM - 6666|0 
ERROR: update_lead NOT AN ALLOWED LIST ID - 7275551212|98762





--------------------------------------------------------------------------------
add_user - adds a user to the system

NOTE: api user for this function must have user_level set to 8 or higher and "modify users" enabled

REQUIRED FIELDS-
agent_user -		2-20 characters (for auto-generated send 'AUTOGENERATED')
agent_pass -		1-20 characters
agent_user_level -	1 through 9, one digit only
agent_full_name -	1-50 characters
agent_user_group -	1-20 characters, must be a valid user group

OPTIONAL FIELDS-
phone_login -		1-20 characters
phone_pass -		1-20 characters
hotkeys_active -	0 or 1
voicemail_id -		1-10 digits
email -			1-100 characters
custom_one -		1-100 characters
custom_two -		1-100 characters
custom_three -		1-100 characters
custom_four -		1-100 characters
custom_five -		1-100 characters
wrapup_seconds_override - number from -1 to 9999
agent_choose_ingroups - 0 or 1, default 1
agent_choose_blended -	0 or 1, default 1
closer_default_blended - 0 or 1, default 0
in_groups -		pipe-delimited list of inbound groups to make selected for the user "SALESLINE|SUPPORT"

NOTE: This function does not work with Vtiger integration

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=add_user&user=6666&pass=1234&agent_user=1000&agent_pass=9999&agent_user_level=1&agent_full_name=Testing+Person&agent_user_group=AGENTS
http://server/vicidial/non_agent_api.php?source=test&function=add_user&user=6666&pass=1234&agent_user=1000&agent_pass=9999&agent_user_level=1&agent_full_name=Testing+Person&agent_user_group=AGENTS&phone_login=100&phone_pass=test&hotkeys_active=1&voicemail_id=&custom_one=jjjj&custom_two=yyyy&custom_three=kkk&custom_four=wwww&custom_five=hhhh

Example responses:
ERROR: add_user USER DOES NOT HAVE PERMISSION TO ADD USERS - 6666|0
ERROR: add_user YOU MUST USE ALL REQUIRED FIELDS - 6666|1000||||
ERROR: add_user USER DOES NOT HAVE PERMISSION TO ADD USERS IN THIS USER LEVEL - 6666|8
ERROR: add_user USER GROUP DOES NOT EXIST - 6666|ADFHR
ERROR: add_user USER ALREADY EXISTS - 6666|6666
SUCCESS: add_user USER HAS BEEN ADDED - 6666|1000|1234|8|Testing+Person|AGENTS






--------------------------------------------------------------------------------
copy_user - copies an existing user in the system to a new user ID and name

NOTE: api user for this function must have user_level set to 8 or higher and "modify users" enabled

REQUIRED FIELDS-
agent_user -		2-20 characters (for auto-generated send 'AUTOGENERATED')
agent_pass -		1-20 characters
agent_full_name -	1-50 characters
source_user -		2-20 characters (must be an existing user ID in the system)

NOTE: This function does not work with Vtiger integration

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=copy_user&user=6666&pass=1234&agent_user=1000&agent_pass=9999&source_user=5555&agent_full_name=Copy+Person

Example responses:
ERROR: copy_user USER DOES NOT HAVE PERMISSION TO COPY USERS - 6666|0
ERROR: copy_user YOU MUST USE ALL REQUIRED FIELDS - 6666|1000|5555|||
ERROR: copy_user USER DOES NOT HAVE PERMISSION TO COPY USERS IN THIS USER LEVEL - 9|8
ERROR: copy_user SOURCE USER DOES NOT EXIST - 6666|5555
ERROR: copy_user USER GROUP DOES NOT EXIST - 6666|ADFHR
ERROR: copy_user USER ALREADY EXISTS - 6666|6666
SUCCESS: copy_user USER HAS BEEN COPIED - 6666|1000|1234|Testing+Person|5555





--------------------------------------------------------------------------------
update_user - updates or deletes a user entry already in the system

NOTE: api user for this function must have user_level set to 8 or higher and "modify user" enabled

REQUIRED FIELDS-
agent_user -		2-20 characters

OPTIONAL FIELDS-
agent_pass -		1-20 characters
agent_user_level -	1 through 9, one digit only
agent_full_name -	1-50 characters
agent_user_group -	1-20 characters, must be a valid user group
phone_login -		1-20 characters
phone_pass -		1-20 characters
hotkeys_active -	0 or 1
voicemail_id -		1-10 digits
email -			1-100 characters
custom_one -		1-100 characters
custom_two -		1-100 characters
custom_three -		1-100 characters
custom_four -		1-100 characters
custom_five -		1-100 characters
active -		'Y' or 'N'
wrapup_seconds_override - number from -1 to 9999
campaign_rank -		from -9 to 9, this will change the campaign rank for this user on all campaigns
campaign_grade -	from 1 to 10, this will change the campaign grade for this user on all campaigns
camp_rg_only -		0 or 1, this will set only one campaign rank/grade for this user, requires campaign_id to be set
campaign_id -		only required for use with camp_rg_only

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=update_user&user=6666&pass=1234&agent_user=2424&agent_pass=9876
http://server/vicidial/non_agent_api.php?source=test&function=update_user&user=6666&pass=1234&agent_user=2424&agent_user_level=3
http://server/vicidial/non_agent_api.php?source=test&function=update_user&user=6666&pass=1234&agent_user=2424&agent_full_name=Testing&hotkeys_active=1&active=Y
http://server/vicidial/non_agent_api.php?source=test&function=update_user&user=6666&pass=1234&agent_user=2424&campaign_rank=3&campaign_grade=4

Example responses:
ERROR: update_user USER DOES NOT HAVE PERMISSION TO UPDATE USERS - 6666|0
ERROR: update_user USER DOES NOT HAVE PERMISSION TO DELETE USERS - 6666|7878
ERROR: update_user YOU MUST USE ALL REQUIRED FIELDS - 6666||
ERROR: update_user USER DOES NOT EXIST - 6666|78789
ERROR: update_user USER DOES NOT HAVE PERMISSION TO UPDATE THIS USER - 6666|7878
ERROR: update_user YOU MUST USE A VALID FULL NAME, THIS IS AN OPTIONAL FIELD - 6666|
ERROR: update_user YOU MUST USE A VALID USER LEVEL, THIS IS AN OPTIONAL FIELD - 6666|0|9|1
ERROR: update_user YOU MUST USE A VALID USER GROUP, THIS IS AN OPTIONAL FIELD - 6666|FAKEGROUP|0
ERROR: update_user YOU MUST USE A VALID PHONE LOGIN, THIS IS AN OPTIONAL FIELD - 6666|fakephone|0|0
ERROR: update_user YOU MUST USE A VALID PHONE PASSWORD, THIS IS AN OPTIONAL FIELD - 6666|0
ERROR: update_user YOU MUST USE A VALID HOTKEYS SETTING, THIS IS AN OPTIONAL FIELD - 6666|2
ERROR: update_user YOU MUST USE A VALID VOICEMAIL ID, THIS IS AN OPTIONAL FIELD - 6666
ERROR: update_user YOU MUST USE A VALID EMAIL, THIS IS AN OPTIONAL FIELD - 6666
ERROR: update_user YOU MUST USE A VALID CUSTOM ONE, THIS IS AN OPTIONAL FIELD - 6666
ERROR: update_user YOU MUST USE A VALID CUSTOM TWO, THIS IS AN OPTIONAL FIELD - 6666
ERROR: update_user YOU MUST USE A VALID CUSTOM THREE, THIS IS AN OPTIONAL FIELD - 6666
ERROR: update_user YOU MUST USE A VALID CUSTOM FOUR, THIS IS AN OPTIONAL FIELD - 6666
ERROR: update_user YOU MUST USE A VALID CUSTOM FIVE, THIS IS AN OPTIONAL FIELD - 6666
ERROR: update_user ACTIVE MUST BE Y OR N, THIS IS AN OPTIONAL FIELD - 6666
ERROR: update_user wrapup_seconds_override MUST BE A VALID DIGIT BETWEEN -1 AND 9999, THIS IS AN OPTIONAL FIELD - 6666|-2
ERROR: update_user YOU MUST USE A VALID CAMPAIGN RANK, THIS IS AN OPTIONAL FIELD - 6666|0
ERROR: update_user YOU MUST USE A VALID CAMPAIGN GRADE, THIS IS AN OPTIONAL FIELD - 6666|0
ERROR: update_user NO UPDATES DEFINED - 6666|0
NOTICE: update_user USER CAMPAIGN RANKS HAVE BEEN UPDATED - 6666|9|10
NOTICE: update_user USER CAMPAIGN RANKS HAVE BEEN UPDATED - 6666|9|10|TESTCAMP|1
SUCCESS: update_user USER HAS BEEN UPDATED - 6666|7878
SUCCESS: update_user USER HAS BEEN DELETED - 6666|7878





--------------------------------------------------------------------------------
add_group_alias - adds group alias to the system

NOTE: api user for this function must have user_level set to 8 or higher and "ast admin access" enabled

REQUIRED FIELDS-
caller_id_number -	6-20 characters

OPTIONAL FIELDS-
group_alias_id -	3-30 characters, no spaces or punctuation allowed, if not defined, caller_id_number is used
group_alias_name -	3-50 characters, if not defined, caller_id_number is used
caller_id_name -	6-20 characters, if not defined '' is default
active -		'Y' or 'N', if not defined 'Y' is default
admin_user_group -	a valid user group or '---ALL---' is used as default

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=add_group_alias&user=6666&pass=1234&caller_id_number=7275551212
http://server/vicidial/non_agent_api.php?source=test&function=add_group_alias&user=6666&pass=1234&caller_id_number=7275551212&group_alias_id=test_group_alias&group_alias_name=test+group+alias

Example responses:
ERROR: add_group_alias USER DOES NOT HAVE PERMISSION TO ADD GROUP ALIASES - 6666|0
ERROR: add_group_alias YOU MUST USE ALL REQUIRED FIELDS - 6666|||1000
ERROR: add_group_alias GROUP ALIAS ALREADY EXISTS - 6666|7275551212
SUCCESS: add_group_alias GROUP ALIAS HAS BEEN ADDED - 6666|7275551212|test_group_alias|test group alias





--------------------------------------------------------------------------------
add_dnc_phone - adds phone number to a DNC list in the system

NOTE: api user for this function must have user_level set to 8 or higher and "modify lists" enabled

REQUIRED FIELDS-
phone_number -	6-20 characters
campaign_id -	2-30 characters, should be a valid campaign ID or "SYSTEM_INTERNAL" for the internal DNC list

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=add_dnc_phone&user=6666&pass=1234&phone_number=7275551212&campaign_id=TESTCAMP
http://server/vicidial/non_agent_api.php?source=test&function=add_dnc_phone&user=6666&pass=1234&phone_number=7275551212&campaign_id=SYSTEM_INTERNAL

Example responses:
ERROR: add_dnc_phone USER DOES NOT HAVE PERMISSION TO ADD DNC NUMBERS - 6666|0
ERROR: add_dnc_phone YOU MUST USE ALL REQUIRED FIELDS - 6666|||1000
ERROR: add_dnc_phone DNC NUMBER ALREADY EXISTS - 6666|7275551212
SUCCESS: add_dnc_phone DNC NUMBER HAS BEEN ADDED - 6666|7275551212|TESTCAMP





--------------------------------------------------------------------------------
add_fpg_phone - adds phone number to a Filter Phone Group in the system

NOTE: api user for this function must have user_level set to 8 or higher and "modify lists" enabled

REQUIRED FIELDS-
phone_number -	6-20 characters
group -		2-30 characters, should be a valid Filter Phone Group ID in the system

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=add_fpg_phone&user=6666&pass=1234&phone_number=7275551212&group=TEST_IN_FILTER

Example responses:
ERROR: add_fpg_phone USER DOES NOT HAVE PERMISSION TO ADD FILTER PHONE GROUP NUMBERS - 6666|0
ERROR: add_fpg_phone YOU MUST USE ALL REQUIRED FIELDS - 6666|||1000
ERROR: add_fpg_phone FILTER PHONE GROUP DOES NOT EXIST - 6666|TEST_IN_FILTER2
ERROR: add_fpg_phone FILTER PHONE GROUP NUMBER ALREADY EXISTS - 6666|7275551212|TEST_IN_FILTER
SUCCESS: add_fpg_phone FILTER PHONE GROUP NUMBER HAS BEEN ADDED - 6666|7275551212|TEST_IN_FILTER




--------------------------------------------------------------------------------
add_phone - adds a phone to the system

NOTE: api user for this function must have user_level set to 8 or higher and "ast admin access" enabled

REQUIRED FIELDS-
extension -		2-100 characters
dialplan_number -	1-20 digits
voicemail_id -		1-10 digits
phone_login -		1-20 characters
phone_pass -		1-20 characters
server_ip -		7-15 characters, must be a valid server_ip
protocol -		Must be one of these: 'IAX2','SIP','Zap','EXTERNAL'
registration_password -	1-20 characters
phone_full_name -	1-50 characters
local_gmt -		timezone setting, not adjusting for DST, default: '-5.00'
outbound_cid -		1-20 digits

OPTIONAL FIELDS-
phone_context -		a phone context, default is 'default'
email -			1-100 characters
admin_user_group -	a valid user group or '---ALL---'
is_webphone -		Y, N or Y_API_LAUNCH: whether this phone should be treated as a webphone, default is N.
webphone_auto_answer -	Y or N: whether this phone should answer as soon as the call to the agent is placed, default is N.
use_external_server_ip -	Y or N: whether this phone should use the server's external IP for registration, default is N.
template_id -		1-15 characters, if defined it must be a valid template in the system
on_hook_agent -		Y or N: should this phone be treated as on-hook when an agent logs in with it, default is N.

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=add_phone&user=6666&pass=1234&extension=55000&dialplan_number=55000&voicemail_id=55000&phone_login=55000&phone_pass=test&server_ip=192.168.198.5&protocol=SIP&registration_password=test&phone_full_name=Extension+55000&local_gmt=-5.00&outbound_cid=7275551212
http://server/vicidial/non_agent_api.php?source=test&function=add_phone&user=6666&pass=1234&extension=55000&dialplan_number=55000&voicemail_id=55000&phone_login=55000&phone_pass=test&server_ip=192.168.198.5&protocol=SIP&registration_password=test&phone_full_name=Extension+55000&local_gmt=-5.00&outbound_cid=7275551212&phone_context=default&email=test@test.com

Example responses:
ERROR: add_phone USER DOES NOT HAVE PERMISSION TO ADD PHONES - 6666|0
ERROR: add_phone YOU MUST USE ALL REQUIRED FIELDS - 6666|1000|||||||||||
ERROR: add_phone SERVER DOES NOT EXIST - 6666|10.0.9.9
ERROR: add_phone PHONE ALREADY EXISTS ON THIS SERVER - 6666|10.0.9.8|cc101
ERROR: add_phone PHONE LOGIN ALREADY EXISTS - 6666|cc100
ERROR: add_phone YOU MUST USE A VALID TIMEZONE - 6666|-5
ERROR: add_phone TEMPLATE ID DOES NOT EXIST, THIS IS AN OPTIONAL FIELD - 6666|cc100|batch
SUCCESS: add_phone PHONE HAS BEEN ADDED - 6666|cc100|10.0.9.8|SIP|100





--------------------------------------------------------------------------------
update_phone - updates or deletes a phone entry already in the system

NOTE: api user for this function must have user_level set to 8 or higher and "ast admin access" enabled

REQUIRED FIELDS-
extension -		2-100 characters
server_ip -		7-15 characters, must be a valid server_ip

SETTINGS FIELDS-
delete_phone -		Y or N, Setting this to Y will delete the phone from the system, default is N.

EDITABLE FIELDS- 
dialplan_number -	1-20 digits
voicemail_id -		1-10 digits
phone_login -		1-20 characters
phone_pass -		1-20 characters
protocol -		Must be one of these: 'IAX2','SIP','Zap','EXTERNAL'
registration_password -	1-20 characters
phone_full_name -	1-50 characters
local_gmt -		timezone setting, not adjusting for DST, default: '-5.00'
outbound_cid -		6-18 digits
outbound_alt_cid -	6-18 digits, to clear this field send '--BLANK--'
phone_context -		a phone context, default is 'default'
email -			5-100 characters, email address for voicemail, to clear this field send '--BLANK--'
admin_user_group -	a valid user group or '---ALL---'
phone_ring_timeout -	1-3 digits, default '60'
delete_vm_after_email - Y or N, default 'N'
is_webphone -		Y, N or Y_API_LAUNCH: whether this phone should be treated as a webphone, default is N.
webphone_auto_answer -	Y or N: whether this phone should answer as soon as the call to the agent is placed, default is N.
use_external_server_ip -	Y or N: whether this phone should use the server's external IP for registration, default is N.
template_id -		1-15 characters, if defined it must be a valid template in the system
on_hook_agent -		Y or N: should this phone be treated as on-hook when an agent logs in with it, default is N.

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=update_phone&user=6666&pass=1234&extension=55000&dialplan_number=55000&voicemail_id=55000&phone_login=55000&phone_pass=test&server_ip=192.168.198.5&protocol=SIP&registration_password=test&phone_full_name=Extension+55000&local_gmt=-5.00&outbound_cid=7275551212
http://server/vicidial/non_agent_api.php?source=test&function=update_phone&user=6666&pass=1234&extension=55000&dialplan_number=55000&voicemail_id=55000&phone_login=55000&phone_pass=test&server_ip=192.168.198.5&protocol=SIP&registration_password=test&phone_full_name=Extension+55000&local_gmt=-5.00&outbound_cid=7275551212&phone_context=default&email=test@test.com
http://server/vicidial/non_agent_api.php?source=test&function=update_phone&user=6666&pass=1234&extension=55000&server_ip=192.168.198.5&delete_phone=Y

Example responses:
ERROR: update_phone USER DOES NOT HAVE PERMISSION TO UPDATE PHONES - 6666|0
ERROR: update_phone YOU MUST USE ALL REQUIRED FIELDS - 6666||
ERROR: update_phone SERVER DOES NOT EXIST - 6666|10.0.9.9
ERROR: update_phone PHONE DOES NOT EXIST ON THIS SERVER - 6666|10.0.9.8|cc101
ERROR: update_phone LOGIN ALREADY EXISTS - 6666|x101
ERROR: update_phone YOU MUST USE A VALID TIMEZONE - 6666|-99
ERROR: update_phone YOU MUST USE A VALID DIALPLAN NUMBER - 6666|0
ERROR: update_phone YOU MUST USE A VALID VOICEMAIL NUMBER - 6666|0
ERROR: update_phone YOU MUST USE A VALID PHONE PASSWORD - 6666|0
ERROR: update_phone YOU MUST USE A VALID REGISTRATION PASSWORD - 6666|0
ERROR: update_phone YOU MUST USE A VALID PROTOCOL - 6666|0
ERROR: update_phone YOU MUST USE A VALID PHONE NAME - 6666|0
ERROR: update_phone YOU MUST USE A VALID PHONE CONTEXT - 6666|0
ERROR: update_phone YOU MUST USE A VALID EMAIL - 6666|x.com
ERROR: update_phone NO UPDATES DEFINED - 6666|0
ERROR: update_phone ACTIVE MUST BE Y OR N, THIS IS AN OPTIONAL FIELD - 6666|0
ERROR: update_phone OUTBOUND CID MUST BE FROM 6 TO 18 DIGITS, THIS IS AN OPTIONAL FIELD - 6666|0
ERROR: update_phone OUTBOUND ALT CID MUST BE FROM 6 TO 18 DIGITS, THIS IS AN OPTIONAL FIELD - 6666|0
ERROR: update_phone USER GROUP MUST BE FROM 2 TO 20 CHARACTERS, THIS IS AN OPTIONAL FIELD - 6666|0
ERROR: update_phone PHONE RING TIMEOUT MUST BE FROM 2 TO 180 SECONDS, THIS IS AN OPTIONAL FIELD - 6666|0
ERROR: update_phone DELETE VOICEMAIL AFTER EMAIL MUST BE Y OR N, THIS IS AN OPTIONAL FIELD - 6666|0
ERROR: update_phone TEMPLATE ID DOES NOT EXIST, THIS IS AN OPTIONAL FIELD - 6666|cc100|batch
SUCCESS: update_phone PHONE HAS BEEN UPDATED - 6666|cc100|10.0.9.8|SIP|100
SUCCESS: update_phone PHONE HAS BEEN DELETED - 6666|cc100|10.0.9.8|
NOTICE: update_phone USER DOES NOT HAVE PERMISSION TO DELETE PHONES - 6666|7878





--------------------------------------------------------------------------------
add_phone_alias - adds a phone alias entry to the system

NOTE: api user for this function must have user_level set to 8 or higher and "ast admin access" enabled

REQUIRED FIELDS-
alias_id -		2-20 characters
phone_logins -		2-255 characters (phone logins separated by commas)
alias_name -		1-50 characters

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=add_phone_alias&user=6666&pass=1234&alias_id=xyz100&alias_name=XYZ+testing&phone_logins=100a,100b

Example responses:
ERROR: add_phone_alias USER DOES NOT HAVE PERMISSION TO ADD PHONE ALIASES - 6666|0
ERROR: add_phone_alias YOU MUST USE ALL REQUIRED FIELDS - 6666||||
ERROR: add_phone_alias PHONE ALIAS ALREADY EXISTS - 6666|x101
ERROR: add_phone_alias PHONE DOES NOT EXIST - 6666|cc100
SUCCESS: add_phone_alias PHONE ALIAS HAS BEEN ADDED - 6666|x100|testing_x100|cc100,bb100





--------------------------------------------------------------------------------
update_phone_alias - updates or deletes a phone alias entry already in the system

NOTE: api user for this function must have user_level set to 8 or higher and "ast admin access" enabled

REQUIRED FIELDS-
alias_id -		2-20 characters
phone_logins -	2-255 characters (phone logins separated by commas)
alias_name -		1-50 characters

SETTINGS FIELDS-
delete_alias -		Y or N, Setting this to Y will delete the phone alias from the system, default is N.

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=update_phone_alias&user=6666&pass=1234&alias_id=xyz100&alias_name=XYZ+testing+X&phone_logins=100a,100b
http://server/vicidial/non_agent_api.php?source=test&function=update_phone_alias&user=6666&pass=1234&alias_id=xyz100&delete_alias=Y

Example responses:
ERROR: update_phone_alias USER DOES NOT HAVE PERMISSION TO UPDATE PHONE ALIASES - 6666|0
ERROR: update_phone_alias YOU MUST USE ALL REQUIRED FIELDS - 6666||||
ERROR: update_phone_alias PHONE ALIAS DOES NOT EXIST - 6666|x101
ERROR: update_phone_alias YOU MUST USE A VALID ALIAS NAME - 6666|x
ERROR: update_phone_alias PHONE DOES NOT EXIST - 6666|cc100
SUCCESS: update_phone_alias PHONE ALIAS HAS BEEN UPDATED - 6666|x100|
SUCCESS: update_phone_alias PHONE ALIAS HAS BEEN DELETED - 6666|x100|





--------------------------------------------------------------------------------
server_refresh - forces a conf file refresh on all telco servers in the cluster

NOTE: api user for this function must have user_level set to 8 or higher and "ast admin access" enabled

REQUIRED FIELDS-
stage -		"REFRESH"

Example URL string for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=server_refresh&user=6666&pass=1234&stage=REFRESH

Example responses:
ERROR: server_refresh USER DOES NOT HAVE PERMISSION TO REFRESH SERVERS - 6666|0
ERROR: server_refresh YOU MUST USE ALL REQUIRED FIELDS - 6666|
SUCCESS: server_refresh SERVER REFRESH HAS BEEN TRIGGERED - 6666|1





--------------------------------------------------------------------------------
add_list - adds a list to the system

NOTE: api user for this function must have user_level set to 8 or higher and "modify lists" enabled

REQUIRED FIELDS-
list_id -		2-14 digits
list_name -		6-30 characters
campaign_id -		2-8 characters, must be a valid campaign_id

OPTIONAL FIELDS-
active -		Must be one of these: 'Y','N', will default to 'N'
list_description -	up to 255 characters, to empty this field, set to --BLANK--
outbound_cid -		6-20 digits
script -		1-10 characters, must be a valid script
am_message -		2-100 characters
drop_inbound_group -	1-10 characters, must be a valid in-group
web_form_address -	6-100 characters
web_form_address_two -	6-100 characters
web_form_address_three - 6-100 characters
reset_time -		4-100 characters, must be in valid 4-digit groups of 24-hour time (i.e. 0900-1700-2359)
tz_method -		one of the following: COUNTRY_AND_AREA_CODE,POSTAL_CODE,NANPA_PREFIX,OWNER_TIME_ZONE_CODE default is COUNTRY_AND_AREA_CODE
local_call_time -	must be a valid call time ID in the system or 'campaign' which is the default
expiration_date -	10 characters, must be in valid date format YYYY-MM-DD (i.e. 2012-11-25)
xferconf_one -		Transfer - Conf Number Override 1: 1-50 characters
xferconf_two -		Transfer - Conf Number Override 2: 1-50 characters
xferconf_three -	Transfer - Conf Number Override 3: 1-50 characters
xferconf_four -		Transfer - Conf Number Override 4: 1-50 characters
xferconf_five -		Transfer - Conf Number Override 5: 1-50 characters
custom_fields_copy -	A valid list ID with custom fields to be copied to this new list, 2-14 digits
custom_copy_method -	(APPEND,UPDATE,REPLACE) method for how to perform the 'custom_fields_copy', default is APPEND
				APPEND - the only 100% safe method, will not result in any lost existing data, only adds fields not present in the new list
				UPDATE - will only update existing custom fields definitions to match the source list
				REPLACE - will delete all existing custom field lead data in new list and start clean
NOTES: 
 - for the web form addresses, you can URL encode them to include standard URL special characters
 - to use the 'custom_fields_copy' option, the API user must have the "Custom Fields Modify" user option enabled


Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&function=add_list&user=6666&pass=1234&list_id=1101&list_name=Test+API+list&campaign_id=TESTCAMP
http://server/vicidial/non_agent_api.php?source=test&function=add_list&user=6666&pass=1234&list_id=1101&list_name=Test+API+list&campaign_id=TESTCAMP&active=N&outbound_cid=7275551212&script=DEMOSCRIPT&am_message=8304&drop_inbound_group=SALESLINE&web_form_address=http://www.vicidial.org/?testing=hghg
http://server/vicidial/non_agent_api.php?source=test&function=add_list&user=6666&pass=1234&list_id=303&list_name=Test+API+list+3&campaign_id=TESTCAMP&custom_fields_copy=222

Example responses:
ERROR: add_list USER DOES NOT HAVE PERMISSION TO ADD LISTS - 6666|0
ERROR: add_list YOU MUST USE ALL REQUIRED FIELDS - 6666|1000||
ERROR: add_list CAMPAIGN DOES NOT EXIST - 6666|TESTCIMP
ERROR: add_list LIST ALREADY EXISTS - 6666|1101
ERROR: add_list SCRIPT DOES NOT EXIST, THIS IS AN OPTIONAL FIELD - 6666|TESTSCRIPT
ERROR: add_list IN-GROUP DOES NOT EXIST, THIS IS AN OPTIONAL FIELD - 6666|TEST_IN8
ERROR: add_list RESET TIME IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|012
ERROR: add_list EXPIRATION DATE IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|012
ERROR: add_list LOCAL CALL TIME DOES NOT EXIST, THIS IS AN OPTIONAL FIELD - 6666|012
ERROR: add_list TIME ZONE METHOD IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|012
ERROR: add_list TRANSFER CONF OVERRIDE ONE IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|123456789012345678901234567890123456789012345678901
ERROR: add_list TRANSFER CONF OVERRIDE TWO IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|123456789012345678901234567890123456789012345678901
ERROR: add_list TRANSFER CONF OVERRIDE THREE IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|123456789012345678901234567890123456789012345678901
ERROR: add_list TRANSFER CONF OVERRIDE FOUR IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|123456789012345678901234567890123456789012345678901
ERROR: add_list TRANSFER CONF OVERRIDE FIVE IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|123456789012345678901234567890123456789012345678901
ERROR: add_list CUSTOM FIELDS LIST ID TO COPY FROM DOES NOT EXIST, THIS IS AN OPTIONAL FIELD - 6666|1101|1102|0
ERROR: add_list CUSTOM FIELDS LIST ID TO COPY FROM HAS NO CUSTOM FIELDS, THIS IS AN OPTIONAL FIELD - 6666|1101|1102|0
ERROR: add_list USER DOES NOT HAVE PERMISSION TO MODIFY CUSTOM FIELDS, THIS IS AN OPTIONAL FIELD - 6666|1101|1102|0
SUCCESS: add_list LIST HAS BEEN ADDED - 6666|1101|TESTCAMP





--------------------------------------------------------------------------------
update_list - updates list information in the vicidial_lists table

NOTE: api user for this function must have user_level set to 8 or higher and "modify lists" enabled

REQUIRED FIELDS-
list_id -		must be all numbers, 2-14 digits
source -		description of what originated the API call (maximum 20 characters)

SETTINGS FIELDS-
insert_if_not_found -	Y or N, will attempt to insert as a new list if no match is found, default is N.
			If list is not found, the function will change to add_list and be processed in that way
reset_list -		Y or N, Setting this to Y will reset the Called-Since-Last-Reset flag for all of the leads in this list, default is N.
delete_list -		Y or N, Setting this to Y will delete the list from the vicidial_lists table, default is N.
delete_leads -		Y or N, Setting this to Y will delete all of the leads with this list_id from the vicidial_list table, default is N.

EDITABLE FIELDS- 
list_name -		6-30 characters
list_description -	up to 255 characters, to empty this field, set to --BLANK--
campaign_id -		2-8 characters, must be a valid campaign_id
active -		Must be one of these: 'Y','N', will default to 'N'
outbound_cid -		6-20 digits
script -		1-10 characters, must be a valid script
am_message -		2-100 characters
drop_inbound_group -	1-10 characters, must be a valid in-group
web_form_address -	6-100 characters
web_form_address_two -	6-100 characters
web_form_address_three - 6-100 characters
reset_time -		4-100 characters, must be in valid 4-digit groups of 24-hour time (i.e. 0900-1700-2359)
tz_method -		one of the following: COUNTRY_AND_AREA_CODE,POSTAL_CODE,NANPA_PREFIX,OWNER_TIME_ZONE_CODE default is COUNTRY_AND_AREA_CODE
local_call_time -	must be a valid call time ID in the system or 'campaign' which is the default
expiration_date -	10 characters, must be in valid date format YYYY-MM-DD (i.e. 2012-11-25)
xferconf_one -		Transfer - Conf Number Override 1: 1-50 characters
xferconf_two -		Transfer - Conf Number Override 2: 1-50 characters
xferconf_three -	Transfer - Conf Number Override 3: 1-50 characters
xferconf_four -		Transfer - Conf Number Override 4: 1-50 characters
xferconf_five -		Transfer - Conf Number Override 5: 1-50 characters
custom_fields_copy -	A valid list ID with custom fields to be copied to the list being updated, 2-14 digits
custom_copy_method -	(APPEND,UPDATE,REPLACE) method for how to perform the 'custom_fields_copy', default is APPEND
				APPEND - the only 100% safe method, will not result in any lost existing data, only adds fields not present in the destination list
				UPDATE - will only update existing custom fields definitions to match the source list, will not add new fields
				REPLACE - will delete all existing custom field lead data in destination list and start clean with new fields
NOTES: 
 - in order to set a field to empty('') set it equal to --BLANK--, i.e. "&drop_inbound_group=--BLANK--"
 - please use no special characters like apostrophes, quotes or amphersands
 - for the web form addresses, you can URL encode them to include standard URL special characters


Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_list&list_id=1101&list_name=Updated+test+API+list
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_list&list_id=1101&active=N
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_list&list_id=1102&active=N&insert_if_not_found=Y&list_name=Updated+test+API+list&campaign_id=TESTCAMP&outbound_cid=7275551212&script=DEMOSCRIPT&am_message=8304&drop_inbound_group=SALESLINE&web_form_address=http://www.vicidial.org/?testing=hghg
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_list&list_id=1102&reset_list=Y
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_list&list_id=1102&delete_list=Y&delete_leads=Y
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_list&list_id=1102&campaign_id=ASTRICON

Example responses:
ERROR: update_list USER DOES NOT HAVE PERMISSION TO UPDATE LISTS - 6666
ERROR: update_list YOU MUST USE ALL REQUIRED FIELDS - 6666|1
ERROR: update_list NOT AN ALLOWED LIST ID - 98762
NOTICE: update_list LIST DOES NOT EXIST, SENDING TO add_list FUNCTION - 6666|1000
ERROR: update_list LIST DOES NOT EXIST - 6666|1000
ERROR: update_list CAMPAIGN DOES NOT EXIST, THIS IS AN OPTIONAL FIELD - 6666|TESTCIMP
ERROR: update_list SCRIPT DOES NOT EXIST, THIS IS AN OPTIONAL FIELD - 6666|TESTSCRIPT
ERROR: update_list IN-GROUP DOES NOT EXIST, THIS IS AN OPTIONAL FIELD - 6666|TEST_IN8
ERROR: update_list LIST NAME MUST BE FROM 6 TO 30 CHARACTERS, THIS IS AN OPTIONAL FIELD - 6666|test
ERROR: update_list ACTIVE MUST BE Y OR N, THIS IS AN OPTIONAL FIELD - 6666|U
ERROR: update_list OUTBOUND CID MUST BE FROM 6 TO 18 DIGITS, THIS IS AN OPTIONAL FIELD - 6666|12345
ERROR: update_list ANSWERING MACHINE MESSAGE MUST LESS THAN 101 DIGITS, THIS IS AN OPTIONAL FIELD - 6666|123...
ERROR: update_list RESET TIME IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|012
ERROR: update_list EXPIRATION DATE IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|012
ERROR: update_list TIME ZONE METHOD IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|012
ERROR: update_list LOCAL CALL TIME DOES NOT EXIST, THIS IS AN OPTIONAL FIELD - 6666|012
ERROR: update_list TRANSFER CONF OVERRIDE ONE IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|123456789012345678901234567890123456789012345678901
ERROR: update_list TRANSFER CONF OVERRIDE TWO IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|123456789012345678901234567890123456789012345678901
ERROR: update_list TRANSFER CONF OVERRIDE THREE IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|123456789012345678901234567890123456789012345678901
ERROR: update_list TRANSFER CONF OVERRIDE FOUR IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|123456789012345678901234567890123456789012345678901
ERROR: update_list TRANSFER CONF OVERRIDE FIVE IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|123456789012345678901234567890123456789012345678901
NOTICE: update_list NO UPDATES DEFINED - 6666|
SUCCESS: update_list LIST HAS BEEN UPDATED - 6666|1101
NOTICE: update_list LEADS IN LIST HAVE BEEN RESET - 6666|1101|324
NOTICE: update_list LIST RESET FAILED, daily reset limit reached: 2 / 2 - 6666|1101
NOTICE: update_list USER DOES NOT HAVE PERMISSION TO DELETE LISTS - 6666|0
NOTICE: update_list LIST HAS BEEN DELETED - 6666|1101|1
NOTICE: update_list USER DOES NOT HAVE PERMISSION TO DELETE LEADS - 6666|0
NOTICE: update_list LEADS IN LIST HAVE BEEN DELETED - 6666|1101|324
NOTICE: update_list CUSTOM FIELDS LIST ID TO COPY FROM DOES NOT EXIST, THIS IS AN OPTIONAL FIELD - 6666|1101|1102|0
NOTICE: update_list CUSTOM FIELDS LIST ID TO COPY FROM HAS NO CUSTOM FIELDS, THIS IS AN OPTIONAL FIELD - 6666|1101|1102|0
NOTICE: update_list USER DOES NOT HAVE PERMISSION TO MODIFY CUSTOM FIELDS, THIS IS AN OPTIONAL FIELD - 6666|1101|1102|0
NOTICE: update_list COPY CUSTOM FIELDS COMMAND SENT - 6666|1101|APPEND





--------------------------------------------------------------------------------
list_info - summary information about a list

NOTE: api user for this function must have user_level set to 8 or higher and "modify lists" enabled

REQUIRED FIELDS-
list_id -		must be all numbers, 2-14 digits
source -		description of what originated the API call (maximum 20 characters)

SETTINGS FIELDS- (optional)
leads_counts -		Y or N, will include the counts of all leads and NEW status leads in the response, default is 'N'
stage -			the format of the exported data: csv, tab, pipe(default)
header -		include a header(YES) or not(NO). This is optional, default is not to include a header


Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=list_info&list_id=1101&leads_counts=Y&header=YES

Example responses:
ERROR: list_info USER DOES NOT HAVE PERMISSION TO MODIFY LISTS - 6666
ERROR: list_info YOU MUST USE ALL REQUIRED FIELDS - 6666|1
ERROR: list_info NOT AN ALLOWED LIST ID - 98762
ERROR: list_info LIST DOES NOT EXIST - 98762

A SUCCESS response will not show "SUCCESS", but instead will just print the results in the following formats:

list_id|list_name|campaign_id|active|list_changedate|list_lastcalldate|expiration_date|resets_today
1101|performance list|TESTCAMP|Y|2019-09-02 08:52:50|2019-11-07 10:12:12|2099-12-31|0|

list_id|list_name|campaign_id|active|list_changedate|list_lastcalldate|expiration_date|resets_today|all_leads_count|new_leads_count
1101|performance list|TESTCAMP|Y|2019-09-02 08:52:50|2019-11-07 10:12:12|2099-12-31|0|59896|3





--------------------------------------------------------------------------------
list_custom_fields - shows the custom data fields that are in a list, or all lists

NOTE: api user for this function must have user_level set to 8 or higher and "modify lists" enabled
NOTE: this output will not include display-only fields in the FORM, only the non-default custom data fields,
      any DISPLAY, SCRIPT and SWITCH form elements will be ignored

REQUIRED FIELDS-
list_id -		must be all numbers, 2-14 digits, OR use "---ALL---" for all lists
source -		description of what originated the API call (maximum 20 characters)

SETTINGS FIELDS- (optional)
stage -			the format of the exported data: csv, tab, pipe(default)
header -		include a header(YES) or not(NO). This is optional, default is not to include a header
custom_order -		the order to put the custom fields into: table_order, alpha_up, alpha_down (default is 'table_order')

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=list_custom_fields&list_id=1101&header=YES

Example responses:
ERROR: list_custom_fields CUSTOM LIST FIELDS ARE NOT ENABLED ON THIS SYSTEM - 6666
ERROR: list_custom_fields USER DOES NOT HAVE PERMISSION TO MODIFY LISTS - 6666
ERROR: list_custom_fields YOU MUST USE ALL REQUIRED FIELDS - 6666|1
ERROR: list_custom_fields NOT AN ALLOWED LIST ID - 98762
ERROR: list_custom_fields LIST DOES NOT EXIST - 98762

A SUCCESS response will not show "SUCCESS", but instead will just print the results in the following formats:

list_id|list_name|campaign_id|active|list_changedate|list_lastcalldate|custom_fields_list_space_delimited
1101|performance list|TESTCAMP|Y|2019-09-02 08:52:50|2019-11-07 10:12:12|fav_color fav_food res_city res_state res_zip





--------------------------------------------------------------------------------
check_phone_number - allows you to check if a phone number is valid and dialable

NOTE: api user for this function must have user_level set to 8 or higher

REQUIRED FIELDS-
phone_number -		must be all numbers, 6-16 digits
phone_code -		must be all numbers, 1-4 digits, defaults to 1 if not set
local_call_time -	must be a valid call time ID in the system

OPTIONAL FIELDS-
postal_code -		must be 5 digits. Only works for USA zipcodes, needed for POSTAL tz_method
state -			must be 2 letters. Only needed if state call time rules enabled
owner -			must be 2-5 letters. Only needed if using TZCODE tz_method

SETTINGS FIELDS-
dnc_check -		Y, N or AREACODE, default is N
campaign_dnc_check -	Y, N or AREACODE, default is N
campaign_id -		2-8 Character campaign ID, required if using campaign_dnc_check
usacan_prefix_check -	Y or N, default is N. Check for a valid 4th digit for USA and Canada phone numbers (cannot be 0 or 1)
usacan_areacode_check -	Y or N, default is N. Check for a valid areacode for USA and Canada phone numbers
nanpa_ac_prefix_check -	Y or N, default is N. Check for a valid NANPA areacode and prefix, if optional NANPA data is on the system
tz_method -		<empty>, POSTAL, TZCODE, NANPA or AREACODE, default is <empty> which will use the country code and AREACODE for time zone lookups
				AREACODE relies on the phone number's areacode
				POSTAL relies on the postal_code field
				TZCODE relies on the owner field being populated with a proper time zone code
				NANPA relies on the optional NANPA areacode prefix data being loaded on your system
				(NOTE: you can do more than one method at once, i.e.: "POSTALTZCODE")


Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=check_phone_number&phone_number=7275551111&call_time=9am-9pm

http://server/vicidial/non_agent_api.php?DB=0&source=test&function=check_phone_number&user=6666&pass=1234&owner=GST&postal_code=81009&phone_number=3125551212&local_call_time=9am-9pm&tz_method=AREACODEPOSTALNANPATZCODE


Example responses:

ERROR: check_phone_number USER DOES NOT HAVE PERMISSION TO CHECK PHONE NUMBERS - |6666|0|
ERROR: check_phone_number THIS IS NOT A VALID CALL TIME - |6666|badct|
ERROR: check_phone_number NANPA options disabled, NANPA prefix data not loaded - 0|6666
ERROR: check_phone_number INVALID PHONE NUMBER LENGTH - 72755|6666 
ERROR: check_phone_number INVALID PHONE NUMBER PREFIX - 72755|6666 
ERROR: check_phone_number INVALID PHONE NUMBER AREACODE - 72755|6666 
ERROR: check_phone_number INVALID PHONE NUMBER NANPA AREACODE PREFIX - 7275551212|6666
ERROR: check_phone_number PHONE NUMBER IN DNC - 7275551112|6666

A success response will show the results for each tz_method used(with 0 or 1 for dialable and the gmt-offset) followed by a hash"#" and then the phone information after:
AREACODE: 1|-6#PHONE: 3125551212|1||||
AREACODE: 1|-6#POSTAL: 1|-7.0#TZCODE: 0|10#NANPA: 1|-6#PHONE: 3125551212|1|81009||GST|





--------------------------------------------------------------------------------
logged_in_agents - list of agents that are logged in to the system

NOTE: api user for this function must have user_level set to 7 or higher and "view reports" enabled

REQUIRED FIELDS-
source -		description of what originated the API call (maximum 20 characters)

OPTIONAL FIELDS-
campaigns -		pipe-delimited list of campaigns to get status information for "TESTCAMP|INBOUND", default is all campaigns shown
user_groups -		pipe-delimited list of user groups to get status information for "ADMIN|AGENTS", default is all user groups shown
show_sub_status -	show agent sub-status and pause_code, requires log lookup, (YES|NO) default is NO

SETTINGS FIELDS-
stage -			the format of the exported data: csv, tab, pipe(default)
header -		include a header(YES) or not(NO). This is optional, default is not to include a header

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=logged_in_agents&stage=csv&header=YES

Example responses:
ERROR: logged_in_agents USER DOES NOT HAVE PERMISSION TO GET AGENT INFO - 6666|0
ERROR: logged_in_agents NO LOGGED IN AGENTS

A SUCCESS response will not show "SUCCESS", but instead will just print the results in the following format:
user|campaign_id|session_id|status|lead_id|callerid|calls_today|full_name|user_group|user_level
6666|TESTCAMP|8600051|PAUSED|1079409||1|Admin|ADMIN|9

user,campaign_id,session_id,status,lead_id,callerid,calls_today,full_name,user_group,user_level,pause_code,sub_status
6666,TESTCAMP,8600051,INCALL,1079409,M2260919190001079409,1,Admin,ADMIN,9,LOGIN,DEAD
4545,TESTCAMP,8600052,PAUSED,0,,0,4545,MIKESGROUP,8,LOGIN,

NOTE: real_time_sub_status field can consist of: DEAD, DISPO, 3-WAY, PARK, RING, PREVIEW, DIAL or it can be empty





--------------------------------------------------------------------------------
call_status_stats - report on number of calls made by campaign and ingroup, with hourly and status breakdowns

NOTE: api user for this function must have user_level set to 8 or higher and "view reports" enabled

REQUIRED FIELDS-
campaigns -		Campaigns to return stats on.  Use "---ALL---" or "ALLCAMPAIGNS" for all campaigns, or use single dash delimiters if 
                        requesting more than one specific campaign

OPTIONAL FIELDS-
query_date -	Date to report on, leave blank to default to today's date.  Must be in YYYY-MM-DD format
ingroups -	List of ingroups to report on.  Leave blank for all ingroups belonging to the campaigns specified in the "campaigns" variable.
		Use dash delimiters if requesting more than one ingroup.
statuses -	List of specific statuses to report on.  Leave blank for all, or use dash delimiters if requesting more than one status.


Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?user=6666&pass=1234&function=call_status_stats&campaigns=---ALL---&query_date=2017-11-24 (returns all campaigns and ingroups for 11/24/17)
http://server/vicidial/non_agent_api.php?user=6666&pass=1234&function=call_status_stats&campaigns=TESTCAMP-TESTCAMP2&query_date=2017-11-24&statuses=NP-TD&ingroups=AGENTDIRECT 
(returns stat counts for TESTCAMP and TESTCAMP2 campaigns, and AGENTDIRECT ingroup, for 11/24/27 for calls dispositioned as NP and TD)


Example responses:
ERROR: call_status_stats INVALID OR MISSING CAMPAIGNS
ERROR: auth USER DOES NOT HAVE PERMISSION TO USE THIS FUNCTION
ERROR: call_status_stats USER DOES NOT HAVE PERMISSION TO VIEW STATS

A SUCCESS response will not show "SUCCESS", but instead will just print the results in the following format:
campaign_id/ingroup|total calls|human answered calls|hourly breakdown(HH-count,HH-count,etc...)|status breakdown(status-count,status-count,etc...)

Hourly and status breakdowns are delimited by comma, and each hour/status count is delimited by dash, as seen below:

TESTCAMP|26|26|00-0,01-1,02-1,03-0,04-0,05-0,06-0,07-0,08-0,09-0,10-0,11-3,12-4,13-2,14-4,15-1,16-0,17-2,18-0,19-0,20-0,21-6,22-1,23-1|NP-4,TD-22|
AGENTDIRECT|2|2|00-0,01-0,02-0,03-0,04-0,05-0,06-0,07-0,08-0,09-0,10-0,11-1,12-1,13-0,14-0,15-0,16-0,17-0,18-0,19-0,20-0,21-0,22-0,23-0|TD-2|





--------------------------------------------------------------------------------
call_dispo_report - call disposition breakdown report


NOTE: api user for this function must have user_level set to 9 and "view reports" enabled

REQUIRED FIELDS(one of the following)-
campaigns -		Campaigns to return stats on.  Use single dash delimiters if requesting more than one specific campaign
ingroups -		In-Groups to return stats on.  Use single dash delimiters if requesting more than one specific inbound group
dids -			DIDs to return stats on.  Use single dash delimiters if requesting more than one specific DID

OPTIONAL FIELDS-
query_date -	Date to report on, leave blank to default to today's date.  Must be in YYYY-MM-DD format
end_date -	Date to report on, leave blank to default to today's date.  Must be in YYYY-MM-DD format
statuses -	List of specific statuses to report on.  Leave blank for all, or use dash delimiters if requesting more than one status.
categories -	List of specific status categories to report on.  Leave blank for all, or use dash delimiters if requesting more than one category.
users -		List of specific users to report on.  Use single dash delimiters if requesting more than one specific user
status_breakdown -	[0,1] Breakdown of all statuses within selected elements, default 0
show_percentages -	[0,1] (Only works if status_breakdown above is enabled), will show percentages of statuses, default 0
file_download -		[0,1] Download as a CSV file, default 0


Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=call_dispo_report&ingroups=TEST_IN3-TEST_IN2&query_date=2018-02-05&end_date=2018-02-07
http://svn.eflo.net:40080/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=call_dispo_report&ingroups=TEST_IN3-TEST_IN2&query_date=2018-02-05&end_date=2018-02-07&status_breakdown=1
http://svn.eflo.net:40080/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=call_dispo_report&did_patterns=7275551113&query_date=2018-02-05&end_date=2018-02-07&status_breakdown=1
http://svn.eflo.net:40080/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=call_dispo_report&ingroups=TEST_IN3-TEST_IN2&query_date=2018-02-05&end_date=2018-02-07&status_breakdown=1&show_percentages=1

Example responses:
ERROR: call_dispo_report INVALID OR MISSING CAMPAIGNS, INGROUPS, OR DIDS
ERROR: call_dispo_report USER DOES NOT HAVE PERMISSION TO USE THIS FUNCTION
ERROR: call_dispo_report USER DOES NOT HAVE PERMISSION TO VIEW STATS

A SUCCESS response will not show "SUCCESS", but instead will just print the results in the following format:

CAMPAIGN,TOTAL CALLS
TEST_IN2,10
TEST_IN3,110
TOTAL,120

CAMPAIGN,TOTAL CALLS,ACFLTR,NP,WAITTO,CLOSOP,DROP,INCALL,RQXFER,TD,TESTAM
TEST_IN2,10,1,0,0,0,5,0,0,0,4
TEST_IN3,110,0,5,13,9,3,1,47,1,31
TOTAL,120,1,5,13,9,8,1,47,1,35

CAMPAIGN,TOTAL CALLS,ACFLTR,NP,WAITTO,CLOSOP,DROP,INCALL,RQXFER,TD,TESTAM
TEST_IN2,10,1 (10.0%),0 (0.0%),0 (0.0%),0 (0.0%),5 (50.0%),0 (0.0%),0 (0.0%),0 (0.0%),4 (40.0%)
TEST_IN3,110,0 (0.0%),5 (4.5%),13 (11.8%),9 (8.2%),3 (2.7%),1 (0.9%),47 (42.7%),1 (0.9%),31 (28.2%)
TOTAL,120,1 (0.8%),5 (4.2%),13 (10.8%),9 (7.5%),8 (6.7%),1 (0.8%),47 (39.2%),1 (0.8%),35 (29.2%)





--------------------------------------------------------------------------------
update_campaign - updates campaign information in the vicidial_campaigns table

NOTE: api user for this function must have user_level set to 8 or higher and "modify campaigns" enabled

REQUIRED FIELDS-
campaign_id -		2-8 characters
source -		description of what originated the API call (maximum 20 characters)

EDITABLE FIELDS- 
campaign_name -		6-40 characters
active -		Must be one of these: 'Y','N'
auto_dial_level -	2-5 characters, must be within valid range
adaptive_maximum_level - 3-20 digits
campaign_vdad_exten -	3-20 characters
hopper_level -		Must be from 1 to 2000
reset_hopper -		Must be one of these: 'Y','N', will default to 'N'
dial_method -		Must be one of these: 'MANUAL','RATIO','INBOUND_MAN','ADAPT_AVERAGE','ADAPT_HARD_LIMIT','ADAPT_TAPERED'
dial_timeout -		Must be from 1 to 120
outbound_cid -		1-20 digits
lead_filter_id -	Must be a valid Filter in the system, or '---NONE---' to not use a filter
xferconf_one -		Transfer - Conf Number 1: 1-50 characters
xferconf_two -		Transfer - Conf Number 2: 1-50 characters
xferconf_three -	Transfer - Conf Number 3: 1-50 characters
xferconf_four -		Transfer - Conf Number 4: 1-50 characters
xferconf_five -		Transfer - Conf Number 5: 1-50 characters
NOTES: 
 - please use no special characters like apostrophes, quotes or amphersands

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_campaign&campaign_id=TESTOUT&campaign_name=Updated+test+API+campaign&auto_dial_level=3.0
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_campaign&campaign_id=TESTOUT&active=N
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_campaign&campaign_id=TESTOUT&reset_hopper=Y

Example responses:
ERROR: update_campaign USER DOES NOT HAVE PERMISSION TO UPDATE CAMPAIGNS - 6666
ERROR: update_campaign YOU MUST USE ALL REQUIRED FIELDS - 6666|1
ERROR: update_campaign NOT AN ALLOWED CAMPAIGN ID - 98762
ERROR: update_campaign CAMPAIGN DOES NOT EXIST - 6666|1000
ERROR: update_campaign AUTO DIAL LEVEL MUST BE FROM 0 TO 18, THIS IS AN OPTIONAL FIELD - 6666|012
ERROR: update_campaign DIAL METHOD IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|12
ERROR: update_campaign ADAPT MAXIMUM DIAL LEVEL MUST BE FROM 1 TO 5 CHARACTERS, THIS IS AN OPTIONAL FIELD - 6666|12
ERROR: update_campaign ROUTING EXTENSION MUST BE FROM 3 TO 20 CHARACTERS, THIS IS AN OPTIONAL FIELD - 6666|12
ERROR: update_campaign HOPPER LEVEL MUST BE FROM 1 TO 2000, THIS IS AN OPTIONAL FIELD - 6666|12345
ERROR: update_campaign DIAL TIMEOUT MUST BE FROM 1 TO 120, THIS IS AN OPTIONAL FIELD - 6666|123
ERROR: update_campaign CAMPAIGN NAME MUST BE FROM 6 TO 40 CHARACTERS, THIS IS AN OPTIONAL FIELD - 6666|test
ERROR: update_campaign CAMPAIGN CID MUST BE FROM 6 TO 20 CHARACTERS, THIS IS AN OPTIONAL FIELD - 6666|test
ERROR: update_campaign CAMPAIGN LEAD FILTER MUST BE A VALID FILTER IN THE SYSTEM, THIS IS AN OPTIONAL FIELD - 6666|test
ERROR: update_campaign TRANSFER CONF NUMBER ONE IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|123456789012345678901234567890123456789012345678901
ERROR: update_campaign TRANSFER CONF NUMBER TWO IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|123456789012345678901234567890123456789012345678901
ERROR: update_campaign TRANSFER CONF NUMBER THREE IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|123456789012345678901234567890123456789012345678901
ERROR: update_campaign TRANSFER CONF NUMBER FOUR IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|123456789012345678901234567890123456789012345678901
ERROR: update_campaign TRANSFER CONF NUMBER FIVE IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|123456789012345678901234567890123456789012345678901
ERROR: update_campaign ACTIVE MUST BE Y OR N, THIS IS AN OPTIONAL FIELD - 6666|U
NOTICE: update_campaign NO UPDATES DEFINED - 6666|
NOTICE: update_campaign HOPPER HAS BEEN RESET - 6666|1101|324
SUCCESS: update_campaign CAMPAIGN HAS BEEN UPDATED - 6666|1101




--------------------------------------------------------------------------------
add_did - adds new Inbound DID entries to the system in the vicidial_inbound_dids table

NOTE: api user for this function must have user_level set to 8 or higher and "Modify DIDs" enabled

REQUIRED FIELDS-
did_pattern -		2-50 characters, must be a valid DID in the system
			   NOTE: special characters in the did_pattern need to be URL encoded, for example the plus sign '+' must be sent as '%2B'
source -		description of what originated the API call (maximum 20 characters)

EDITABLE FIELDS- 
did_description -	6-50 characters
active -		Must be one of these: 'Y','N'
did_route -		must be one of these: 'EXTEN','VOICEMAIL','AGENT','PHONE','IN_GROUP','CALLMENU','VMAIL_NO_INST'
record_call -		must be one of these: 'Y','N','Y_QUEUESTOP'
extension -		1-50 digits
exten_context -		1-50 characters
voicemail_ext -		1-10 digits
phone_extension -	1-100 characters
server_ip -		must be all numbers and dots, max 15 characters
group -			a valid in-group group_id
menu_id -		a valid call menu menu_id
filter_clean_cid_number - 1-20 characters

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=add_did&did_pattern=%2B1727555112&did_description=Updated+test+API+DID&active=Y
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=add_did&did_pattern=7275553331&did_description=Testing+DID&active=N&did_route=EXTEN&record_call=Y_QUEUESTOP&extension=8300&exten_context=notdefault&voicemail_ext=8301&phone_extension=55000&server_ip=192.168.198.5&group=TEST_IN2&filter_clean_cid_number=R10

Example responses:
ERROR: add_did USER DOES NOT HAVE PERMISSION TO ADD DIDS - 6666
ERROR: add_did YOU MUST USE ALL REQUIRED FIELDS - 6666|1
ERROR: add_did NOT AN ALLOWED DID - 98762
ERROR: add_did DID ALREADY EXISTS - 6666|1000
ERROR: add_did DID DESCRIPTION MUST BE FROM 6 TO 50 CHARACTERS, THIS IS AN OPTIONAL FIELD - 6666|012
ERROR: add_did ACTIVE MUST BE Y OR N, THIS IS AN OPTIONAL FIELD - 6666|U
ERROR: add_did DID ROUTE IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|12
ERROR: add_did RECORD CALL MUST BE Y OR N, THIS IS AN OPTIONAL FIELD - 6666|
ERROR: add_did EXTENSION MUST BE FROM 1 TO 50 CHARACTERS, THIS IS AN OPTIONAL FIELD - 6666|
ERROR: add_did EXTENSION CONTEXT MUST BE FROM 1 TO 50 CHARACTERS, THIS IS AN OPTIONAL FIELD - 6666|
ERROR: add_did VOICEMAIL EXTENSION MUST BE FROM 1 TO 10 CHARACTERS, THIS IS AN OPTIONAL FIELD - 6666|
ERROR: add_did PHONE EXTENSION MUST BE FROM 1 TO 100 CHARACTERS, THIS IS AN OPTIONAL FIELD - 6666|
ERROR: add_did SERVER IP MUST BE A VALID SERVER IN THE SYSTEM, THIS IS AN OPTIONAL FIELD - 6666|0.0.0.0
ERROR: add_did GROUP ID MUST BE A VALID INBOUND GROUP IN THE SYSTEM, THIS IS AN OPTIONAL FIELD - 6666|test
ERROR: add_did MENU ID MUST BE A VALID CALL MENU IN THE SYSTEM, THIS IS AN OPTIONAL FIELD - 6666|test_menu
ERROR: add_did CLEAN CID NUMBER MUST BE FROM 1 TO 20 CHARACTERS, THIS IS AN OPTIONAL FIELD - 6666|
SUCCESS: add_did DID HAS BEEN ADDED - 6666|1101





--------------------------------------------------------------------------------
update_did - updates Inbound DID information in the vicidial_inbound_dids table

NOTE: api user for this function must have user_level set to 8 or higher and "Modify DIDs" enabled

REQUIRED FIELDS-
did_pattern -		2-50 characters, must be a valid DID in the system
			   NOTE: special characters in the did_pattern need to be URL encoded, for example the plus sign '+' must be sent as '%2B'
source -		description of what originated the API call (maximum 20 characters)

EDITABLE FIELDS- 
did_description -	6-50 characters
active -		Must be one of these: 'Y','N'
did_route -		must be one of these: 'EXTEN','VOICEMAIL','AGENT','PHONE','IN_GROUP','CALLMENU','VMAIL_NO_INST'
record_call -		must be one of these: 'Y','N','Y_QUEUESTOP'
extension -		1-50 digits
exten_context -		1-50 characters
voicemail_ext -		1-10 digits
phone_extension -	1-100 characters
server_ip -		must be all numbers and dots, max 15 characters
group -			a valid in-group group_id
menu_id -		a valid call menu menu_id
filter_clean_cid_number - 1-20 characters
delete_did -		Can be one of these: 'Y','N' (default is 'N')

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_did&did_pattern=%2B1727555112&did_description=Updated+test+API+DID&active=Y
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_did&did_pattern=7275553331&did_description=Testing+DID&active=N&did_route=EXTEN&record_call=Y_QUEUESTOP&extension=8300&exten_context=notdefault&voicemail_ext=8301&phone_extension=55000&server_ip=192.168.198.5&group=TEST_IN2&filter_clean_cid_number=R10

Example responses:
ERROR: update_did USER DOES NOT HAVE PERMISSION TO UPDATE DIDS - 6666
ERROR: update_did USER DOES NOT HAVE PERMISSION TO DELETE DIDS - 6666
ERROR: update_did YOU MUST USE ALL REQUIRED FIELDS - 6666|1
ERROR: update_did NOT AN ALLOWED DID - 98762
ERROR: update_did DID DOES NOT EXIST - 6666|1000
ERROR: update_did DID DESCRIPTION MUST BE FROM 6 TO 50 CHARACTERS, THIS IS AN OPTIONAL FIELD - 6666|012
ERROR: update_did ACTIVE MUST BE Y OR N, THIS IS AN OPTIONAL FIELD - 6666|U
ERROR: update_did DID ROUTE IS NOT VALID, THIS IS AN OPTIONAL FIELD - 6666|12
ERROR: update_did RECORD CALL MUST BE Y OR N, THIS IS AN OPTIONAL FIELD - 6666|
ERROR: update_did EXTENSION MUST BE FROM 1 TO 50 CHARACTERS, THIS IS AN OPTIONAL FIELD - 6666|
ERROR: update_did EXTENSION CONTEXT MUST BE FROM 1 TO 50 CHARACTERS, THIS IS AN OPTIONAL FIELD - 6666|
ERROR: update_did VOICEMAIL EXTENSION MUST BE FROM 1 TO 10 CHARACTERS, THIS IS AN OPTIONAL FIELD - 6666|
ERROR: update_did PHONE EXTENSION MUST BE FROM 1 TO 100 CHARACTERS, THIS IS AN OPTIONAL FIELD - 6666|
ERROR: update_did SERVER IP MUST BE A VALID SERVER IN THE SYSTEM, THIS IS AN OPTIONAL FIELD - 6666|0.0.0.0
ERROR: update_did GROUP ID MUST BE A VALID INBOUND GROUP IN THE SYSTEM, THIS IS AN OPTIONAL FIELD - 6666|test
ERROR: update_did MENU ID MUST BE A VALID CALL MENU IN THE SYSTEM, THIS IS AN OPTIONAL FIELD - 6666|test_menu
ERROR: update_did CLEAN CID NUMBER MUST BE FROM 1 TO 20 CHARACTERS, THIS IS AN OPTIONAL FIELD - 6666|
NOTICE: update_did NO UPDATES DEFINED - 6666|
SUCCESS: update_did DID HAS BEEN DELETED - 6666|23|1101
SUCCESS: update_did DID HAS BEEN UPDATED - 6666|1101





--------------------------------------------------------------------------------
update_cid_group_entry - updates CID Group entries in the vicidial_campaign_cid_areacodes table

NOTE: api user for this function must have user_level set to 8 or higher and "Modify Campaigns" enabled

REQUIRED FIELDS-
cid_group_id -		2-20 characters, must be a valid CID Group ID or Campaign ID in the system
source -		description of what originated the API call (maximum 20 characters)
areacode -		This should be either the AREACODE(i.e. "312") or STATE(i.e. "FL") or NONE of the CID Group entry, you can also set this to "---ALL---"
stage -			UPDATE, ADD, DELETE or INFO

OPTIONAL FIELDS-
outbound_cid -		1-20 digits, or (if you want to UPDATE, DELETE or INFO all entries for an areacode, you can set this to "---ALL---" or leave it blank)

EDITABLE FIELDS- 
cid_description -	6-50 characters, default is blank
active -		Must be one of these: 'Y','N', default is 'N'

Example URL strings for API calls:
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_cid_group_entry&stage=INFO&cid_group_id=TESTINX5&areacode=---ALL---
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_cid_group_entry&stage=ADD&cid_group_id=testing_here4&areacode=312&outbound_cid=3125551212&cid_description=Testing+DID&active=N
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_cid_group_entry&stage=DELETE&cid_group_id=testing_here4&areacode=312&outbound_cid=3125551212
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_cid_group_entry&stage=UPDATE&cid_group_id=testing_here4&areacode=312&outbound_cid=3125551212&active=Y
http://server/vicidial/non_agent_api.php?source=test&user=6666&pass=1234&function=update_cid_group_entry&stage=UPDATE&cid_group_id=testing_here4&areacode=312&outbound_cid=---ALL---&active=N&cid_description=Deactivated+for+now

Example responses:
ERROR: update_cid_group_entry USER DOES NOT HAVE PERMISSION TO UPDATE CID GROUPS - 6666
ERROR: update_cid_group_entry YOU MUST USE ALL REQUIRED FIELDS - 6666|1||
ERROR: update_cid_group_entry NOT AN ALLOWED CID GROUP - 98762
ERROR: update_cid_group_entry CID GROUP DOES NOT EXIST - 6666|1000
ERROR: update_cid_group_entry THIS CID GROUP HAS NO ENTRIES - 6666|1000
SUCCESS: update_cid_group_entry CID GROUP INFO AS FOLLOWS - 1101|---ALL---|---ALL---|3
1|727|7275551212|N|FL1 test|0
2|727|7275551313|N|FL1 test|0
4|998|7275551114|N|FL2 test|0

ERROR: update_cid_group_entry YOU MUST DEFINE AN AREACODE WHEN ADDING AND ENTRY - 6666|1000|---ALL---
ERROR: update_cid_group_entry YOU MUST DEFINE A CID NUMBER WHEN ADDING AND ENTRY - 6666|1000|1
ERROR: update_cid_group_entry THIS CID GROUP ENTRY ALREADY EXISTS - 6666|1000|813|8135551212
SUCCESS: update_cid_group_entry CID GROUP ENTRY HAS BEEN ADDED - 6666|1101|813|8135551212|1
SUCCESS: update_cid_group_entry CID GROUP ENTRIES HAVE BEEN DELETED - 6666|1101|813|8135551212|1
ERROR: update_cid_group_entry NO UPDATES DEFINED - 6666|813|8135551212||
SUCCESS: update_cid_group_entry CID GROUP ENTRIES HAVE BEEN UPDATED - 6666|1101|813|8135551212|1



ERROR: NO FUNCTION SPECIFIED
