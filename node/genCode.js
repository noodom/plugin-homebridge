'use strict'

var bases = require('bases/bases');
var bignum = require('bignum');

var HAP_TYPE_IP = 0;
var HAP_TYPE_BLE = 1;
var HAP_TYPE_IP_WAC = 2;

var ACCESSORY_CATEGORY_BRIDGES = 2;

var pin = '03145154';
if(process.argv[2])
	pin = process.argv[2].replace(/-/g, '');
var setupID = 'NEBZ';
if(process.argv[3])
	setupID = process.argv[3];

function generate_setup_payload_uri(category_id, hap_type, setup_code, setup_id) {
	var payload = 0x00;
	payload = bignum(category_id).shiftLeft(31);
	if(hap_type == HAP_TYPE_IP_WAC) payload = payload.or(bignum(1).shiftLeft(30));
	if(hap_type == HAP_TYPE_BLE) payload = payload.or(bignum(1).shiftLeft(29));
	if(hap_type == HAP_TYPE_IP || hap_type == HAP_TYPE_IP_WAC) payload = payload.or(bignum(1).shiftLeft(28));
	payload = payload.or(bignum(setup_code));
	return "X-HM://00" + bases.toBase36(payload).toUpperCase() + setup_id;
}

console.log(generate_setup_payload_uri(ACCESSORY_CATEGORY_BRIDGES, HAP_TYPE_IP, pin, setupID));
