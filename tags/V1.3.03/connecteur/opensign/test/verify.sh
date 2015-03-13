#! /bin/sh


openssl ts -verify -data preuve.txt -in preuve.tsr -CAfile root_ca.crt -untrusted ts.crt 
