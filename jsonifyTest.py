import re

regex = r"\w+\s*=\s*\w+|\w+\s*=\s*\{(.*?\}).\}?"

# get all the matches that arent nested
regex = r"\w+\s=\s\w+\s"

final_arr = {}

test_str = "a = 0\njoepoints = 0\nsidpoints = 0\njoe = {name = Joe, successRate = 71, throws = 0, gamesWon = 0, setsWon = 0, \n  score = 501, set = {0, 0, 0}, winStrategy = {0, 0, 0}}\nsid = {name = Sid, successRate = 73, throws = 0, gamesWon = 0, setsWon = 0, \n  score = 501, set = { 0, 0, 0}, winStrategy = {0, 0, 0}}\nthrowType = {<No data fields>}\n"

#test_str = "name = Joe successRate = 71 score = 501 set = {0 = a 1 = b 2 = c} winStrategy = {0 = d 1 = e 2 = f}"
variable_str = test_str.replace("\n", " ")
print(variable_str)
subst = "$1{$2}, "
variable_str = re.sub(r"(\w+\s=)(\s\w+\s)", "", variable_str, 0)

print(variable_str)

def rec(variable_str, final_arr, parent = "obj"):
	
	variable_str = variable_str.replace(",", "")
	print(variable_str)

	# find the non nested variables
	non_nested = re.finditer(regex, variable_str)

	#print(parent)
	final_arr[parent] = []

	# append the non nested variables
	for matchNum, match in enumerate(non_nested, start=1):
		#print(parent)
		final_arr[parent].append(match.group())

	# work out if theres any nested variables
	nested = re.sub(regex, "", variable_str, 0)

	if nested.strip() == "":
		return
	
	#print(nested)
	
	poses = []
	nested_arr = []

	split = re.finditer(r"\}\s+\w+", nested)

	for matchNum, match in enumerate(split, start=1):
		pos = match.span()[0]
		poses.append(int(pos) + 1)

	last_pos = 0

	for i in poses:
		s = nested[last_pos:i]
		last_pos = i
		nested_arr.append(s)

	s = nested[last_pos:len(nested)]
	nested_arr.append(s)

	#print("looking in nested array")
	for i in nested_arr:
		value = i.split("=", 1)[-1].strip()
		v = value[1:]
		a = v[:-1]
		# print("value " + i.split("=", 1)[0])
		print("value " + a)
		return rec(a, final_arr, parent = i.split("=", 1)[0].strip())
	
rec(variable_str, final_arr)
print(final_arr)
