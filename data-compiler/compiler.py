import csv, json

csv_name 	= [0, 'name']
csv_date 	= [1, 'date']
csv_time 	= [2, 'time']
csv_light 	= [3, 'light']
csv_action 	= [4, 'action']
csv_data 	= [5, 'data']
csv_screen 	= [6, 'screen']

def extract_csv(file):
	data = []
	with open(file, 'rb') as csvfile:
		csvdata = csv.reader(csvfile, delimiter=',', quotechar='"')
		for row in csvdata:
			row_data = {csv_name[1]: 	row[csv_name[0]],
						csv_date[1]: 	row[csv_date[0]],
						csv_time[1]: 	row[csv_time[0]],
						csv_light[1]: 	row[csv_light[0]],
						csv_action[1]: 	row[csv_action[0]],
						csv_data[1]: 	row[csv_data[0]],
						csv_screen[1]:	row[csv_screen[0]]}
			data.append(row_data)
		return data

def to_json(data):
	return json.dumps(data, sort_keys=True, indent=2)
	
data = extract_csv("../data/data-compiler-test/omas-mullis-input.csv")
print(to_json(data))