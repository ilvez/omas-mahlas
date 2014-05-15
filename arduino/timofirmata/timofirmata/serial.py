
from twisted.protocols.basic import LineReceiver

class SerialProtocol(LineReceiver):

    def lineReceived(self, data):
        print(data)

    def turn_on(self, on):
        if not on:
            self.sendLine("I\n")
        else:
            for num in on:
                self.sendLine("I{}\n".format(num))

   
    def turn_off(self, off):
        if not off:
            self.sendLine("O\n")
        else:
            for num in off:
                self.sendLine("O{}\n".format(num))

