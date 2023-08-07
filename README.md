# Phonepe-Whmcs-Module-Master

# Introduction

This integration kit is used in WHMCS PHP E-Commerce Application. This library provides support for Phonepe payment gateway.

# Installation

Copy the files from this plugin into the corresponding folders on your installation, as mentioned below:
 1. Copy the Phonepe/gateways/phonepe.php file into your installation's /module/gateways/ folder
 2. Copy the Phonepe/gateways/callback/phonepe.php file into your installation's /module/gateways/callback folder.
 3. Copy the Phonepe/gateways/phonepe-sdk folder into your /module/gateways folder

# Configuration

Provide the values for the following in the *Configuration Settings* of the Admin Panel.
 1. Merchant ID
 2. Salt Key
 3. Salt Index
 4. Production Url

# Phonepe PG URL Details
	Staging	
		Production URL             => https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1
                Salt Key		   => 099eb0cd-02cf-4e2a-8aca-3e6c6aff0399
		Salt Index		   => 1
                Merchant Id		   => PGTESTPAYUAT

	Production
		Production URL             => https://api.phonepe.com/apis/hermes/pg/v1
