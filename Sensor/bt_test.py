from bluetooth import *
server_sock = BluetoothSocket(RFCOMM)
server_sock.bind(("",PORT_ANY))
server_sock.listen(1)

port = server_sock.getsockname()[1]
uuid = "94f39d29-7d6d-437d-973b-fba39e49d4ee"
advertise_service(server_sock, "BluetoothServerMark", service_id = uuid, service_classes = [uuid, SERIAL_PORT_CLASS], profiles = [SERIAL_PORT_PROFILE])
print "Waiting for connection on RFCOMM channel {0}".format(port)

client_sock, client_info = server_sock.accept()
print "Accepted connection from {0}".format(client_info[0])

try:
    while True:
        data = client_sock.recv(1024)
        if len(data) == 0: break
        print "Received: {0}".format(data)
	client_sock.send('Echo => ' + str(data))
except Exception as e:
    print "Error: {0}".format(repr(e))

print "Disconnected!"
client_sock.close()
server_sock.close()
print "Sockets closed. Bye!"
