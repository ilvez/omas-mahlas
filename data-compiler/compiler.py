import csv, json
from datetime import datetime

class StoryElement:
	'StoryElement corresponds to one row in input CSV'

	def format_time(self, time):
		if (len(time) == 4):
			time = '0' + time
		return time

	def fix_punctuation(self, datetime):
		return datetime.replace(".", ":")
	def __init__(self, row):
		raw_name = row[0]
		raw_datetime = self.fix_punctuation(row[1] + "-" + self.format_time(row[2]))
		if(raw_name != 'nimi'):
			self.name = raw_name
			self.time = datetime.strptime(raw_datetime, '%d:%m:%Y-%H:%M')
			self.light = row[3]
			self.action = row[4]
			self.data = row[5]
			self.screenshot = row[6]
	def __str__(self):
		return str(self.time) + " - " + self.name

def extract_csv(file):
	data = []
	with open(file, 'rb') as csvfile:
		csvdata = csv.reader(csvfile, delimiter=',', quotechar='"')
		for row in csvdata:
			row_data = StoryElement(row)
			data.append(row_data)
		return data

def to_json(data):
	return json.dumps(data, sort_keys=True, indent=2)
	
data = extract_csv("../data/data-compiler-test/omas-mullis-input.csv")
print(data[3])