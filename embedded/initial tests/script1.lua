status = '/"away'
pin=4
id = node.chipid()
gpio.mode(pin,gpio.OUTPUT)
wifi.sta.config("Cameron's iPhone","isles6464")
tmr.alarm(1,2000,1,function()

sk=net.createConnection(net.TCP, 0)
sk:on("receive", function(sck, c) status = string.match(c, '\"....') end )
sk:connect(80,"77.95.37.3")
sk:send("GET /apiv1/status/"..id.."/59.6 HTTP/1.1\r\nHost: eversafe.decision.hosting\r\nConnection: keep-alive\r\nAccept: */*\r\n\r\n")
if status~=nil then 
status = string.sub(status,2)
end
print(status)
if status=="home" then
gpio.write(pin,gpio.HIGH)
else
gpio.write(pin,gpio.LOW)
end
end)