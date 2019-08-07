import time

select_pin = [24, 25, 26, 27]
en_bit = 28

for i in range(0, 4):
    pin_direction = open("/gpio/pin" + str(i + 24) + "/direction", 'w')
    pin_direction.write("out")
    pin_direction.close()


def pin_mask(bit):
    if bit == 0:
        return 1
    if bit == 1:
        return 2
    if bit == 2:
        return 4
    if bit == 3:
        return 8


def write_bit_to_gpio_pin(pin, value):
    return 1


def read_data():
    raw = int(open("/sys/bus/iio/devices/iio:device0/in_voltage0_raw").read())
    scale = float(open("/sys/bus/iio/devices/iio:device0/in_voltage_scale").read())
    a = raw*scale
    #a = raw
    return a

def map_select_gpio_pin(bit):
    if bit == 0:
        return 24
    if bit == 1:
        return 25
    if bit == 2:
        return 26
    if bit == 3:
        return 27


def mux(channel, en=True):
    write_bit_to_gpio_pin(en_bit, ~en)
    s = [0, 0, 0, 0]
    for i in range(0, 4):
        s[i] = (channel & pin_mask(i)) >> i
        write_bit_to_gpio_pin(map_select_gpio_pin(i), s[i])
    return s


def pin():
    for i in range(0, 4):
        if select_bits[i] == 0:
            filename = "/gpio/pin" + str(i + 24) + "/value"
            file = open(filename, 'w')
            file.write("0")
            file.close()
            print select_bits[i]
        if select_bits[i] == 1:
            filename = "/gpio/pin" + str(i + 24) + "/value"
            file = open(filename, 'w')
            file.write("1")
            file.close()
            print select_bits[i]


select_bits = mux(0)
mode = 0
print "Hello"
while True:
    mode = int(raw_input("Select the pins that you want"))
    if mode >= 0 and mode <= 15:
        select_bits = mux(mode)
        pin()
        time.sleep(0.1)
        print select_bits
        a = read_data()
        print a
        print "\n"
    else:
        print "Try again"
        print "\n"

