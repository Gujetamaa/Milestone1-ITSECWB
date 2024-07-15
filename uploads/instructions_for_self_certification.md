First open a file explorer and goto XAMP/apache/config
open ssl.crt, ssl.key, and ssl.csr
and remove the initial files named "server"
go back to folder \uploads and cut server.crt and go back to folder \ssl.crt and paste
go back to folder \uploads and cut server.key and go back to folder \ssl.key and paste
no need to do anything for folder /ssl.csr because it will automatically generate a csr file after testing the https://localhost/Milestone1-ITSECWB/