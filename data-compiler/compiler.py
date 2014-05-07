import csv, json

def extract_csv(file):
	data = []
	with open(file, 'rb') as csvfile:
		csvdata = csv.reader(csvfile, delimiter=',', quotechar='"')
		print(csvdata)
		for row in csvdata:
			print (row)

extract_csv("../test-data/omas-mullis-input.csv")