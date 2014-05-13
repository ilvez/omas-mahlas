#!/usr/bin/env python
# -*- coding: utf-8 -*-

import json

from twisted.internet import reactor, stdio
from twisted.web import server, resource
from twisted.internet.serialport import SerialPort

from .serial import SerialProtocol

class ControlPage(resource.Resource):
    isLeaf = 1

    def __init__(self, serial_proto):
        resource.Resource.__init__(self)
        self.serial_proto = serial_proto
        
    def _send_headers(self, request):
        request.setHeader('Content-type', 'text/plain')
        request.setHeader('Access-Control-Allow-Origin', '*')
        request.setHeader('Access-Control-Allow-Methods', 'POST')
        request.setHeader("Access-Control-Allow-Headers", "X-Requested-With")
        
    def respond(self, data, request):
        try:
            self._send_headers(request)
            request.write(json.dumps(data, encoding="latin-1"))
            request.finish()
        except BaseException as e:
            pass

    def respond_err(self, data, request):
        """Gets an Failure, will pass the dictionary."""
        self.respond(data.value, request)
        
            
    def render_POST(self, request):
        if 'I' in request.args:
            self.serial_proto.turn_on(request.args['I'])
        if 'O' in request.args:
            self.serial_proto.turn_off(request.args['O'])
        self._send_headers(request)
        return json.dumps({"response" : "OK"}, encoding="latin-1")
        
def run(port, baudrate):
    serial_protocol = SerialProtocol()
    page = server.Site(ControlPage(serial_protocol))
    reactor.listenTCP(8000, page)

    #stdio.StandardIO(serial_protocol)
    SerialPort(serial_protocol, port, reactor, baudrate=baudrate)
    
    reactor.run() 

