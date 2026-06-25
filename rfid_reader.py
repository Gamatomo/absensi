import gpio

# Set up the RFID card reader
rfid_reader = gpio.RFIDReader()

# Read data from the RFID card reader
data = rfid_reader.read()

# Print the data
print(data)
