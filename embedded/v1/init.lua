countdown = 3
tmr.alarm(0,1000,1,function()
    print(countdown)
    countdown = countdown-1
    if countdown<1 then
        tmr.stop(0)
        countdown = nil
       if file.open("script1.lua") then
            file.close()
            dofile("script1.lua")
        else
            print("script1.lua not found :(")
        end
    end
end)