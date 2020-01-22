@echo off
rem
rem verisign.crt is just verisign_int.crt + verisign_sec.crt combined.
rem

if "%1"=="" echo Usage: sign_mobileconfig_font file.mobileconfig && goto :eof
if not exist signed mkdir signed
if not exist unsigned mkdir unsigned
t:\util\openssl\bin\openssl smime -inform DER -verify -in %1  -noverify -out unsigned\%1
t:\util\openssl\bin\openssl smime -sign -in unsigned\%1 -out signed\%1 -signer t:cert.pem -inkey t:key.pem -certfile t:verisign.crt -outform der -nodetach

rem c:\openssl-win32\bin\openssl smime -sign -in %1 -out signed\%1 -signer t:cert.pem -inkey t:key.pem -certfile t:verisign-sec.crt -outform der -nodetach
