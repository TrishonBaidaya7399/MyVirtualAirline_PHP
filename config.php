<?php
// vaBase config (version 4.0)
// Join the vaBase community Discord: https://discord.gg/DpCVMgXC4d

//This is your vaBase Live API key that is provided in your welcome email or you can find this by logging in to the vaBase website
const client_api_key = "28409ac7-2b9d-4585-8d3b-c0f680a92e5a";
//

//The full base url and directory in which vaBase Live is installed (Example: http://www.mywebsite.com/)
const website_base_url = "https://wordpress-489688-5401456.cloudwaysapps.com/flight_center/"; //make sure you have an ending / after the the URL
//

//Virtual Airline settings
const virtual_airline_name = "MyAirline";
const airline_admin_email = "darryl@dalvirtual.com";
const fuel_weight_display = 1; //0 = kg, 1 = lb
const cargo_weight_display = 1; //0 = kg, 1 = lb
const pilots_can_book_same_flight = true; //set to false to only allow a flight to be booked by a single pilot. Set to true to allow multiple pilots book same flight
//

//SimBrief Configuration
const simbrief_enabled = true; //Disable SimBrief dispatch by setting to false
const simbrief_api_key = "P8wNcPc2eZFwySzMfYETjxGcrASEvJMY"; //You can obtain an API key by contacting SimBrief: https://www.simbrief.com/home/?page=support
const simbrief_flight_plan_format = "LIDO";
const simbrief_cost_index = 20;
const simbrief_airline = "VAB"; //Airline ICAO/IATA code
const simbrief_extra_fuel_minutes = 20;
const simbrief_custom_fp_remarks = "";
const simbrief_allow_charter = true; //Set to false to disallow charter flights
//

//Discord Webhook Notifications
//Login at vaBase.com and go to your airline dashboard > edit to provide your Discord webhook URL
const enable_discord_pirep_alerts = true;
const enable_discord_new_pilot_alerts = true;
const enable_discord_award_pilot_alerts = true;
const enable_discord_dispatch_flight_alerts = true;
const enable_discord_add_news_alerts = true;
//

//Set file upload directory to where file uploads are saved on the server (ensure write permissions are enabled)
const file_upload_dir = "../uploads/"; //only modify this if you change the default folder structure
//

//Email SMTP settings
const smtp_host = ""; //example: mail.mywebsite.com (for port 587 prefix tls:// and for port 465 prefix ssl:// )
const smtp_port = 587;
const smtp_username = ""; //example: user@mywebsite.com
const smtp_password = "";
const smtp_from_address = ""; //this is the email you would like your recipients to reply to
//

//Site Settings
const enable_cookie_banner = true;
const facebook_footer_link = "#"; //set blank to hide icon
const youtube_footer_link = "#"; //set blank to hide icon
const discord_footer_link = "#"; //set blank to hide icon
const twitter_footer_link = "#"; //set blank to hide icon
const instagram_footer_link = "#"; //set blank to hide icon
//

//PHP global server config settings
if (!isset($_SESSION)) {
    ini_set('session.cookie_httponly', 1);
}
//