#include <iostream>
#include <vector>
#include <algorithm>
#include <map>

using namespace std;


struct Person // person data 
{
public:
	Person(string n, int b, int d)
	{
		name = n;
		birth_year = b;
		death_year = d;
	}

	string name;
	int birth_year;
	int death_year;
};
//------------------------------Utility Functions---------------------------------------------//

bool compare(std::pair <int, int> a, std::pair <int, int> b)
{
	return a.second < b.second;
}
//------------------------------------------------Main Functions--------------------------------------------//

void YearMostPeopleAlive(vector<Person> persons, vector<int> &years)
{
	vector<int> birth;   // birth years
	vector<int>death;   // death years
	map<int, int> year_count;    // range of relevant years
	pair<int, int> max;    // max value of years

	for (size_t i = 0; i < persons.size();i++)
	{
		birth.push_back(persons[i].birth_year);//accumulate birth and death years
		death.push_back(persons[i].death_year);

	}

	sort(birth.begin(), birth.end());  //  Sort the vectors to get the range of relevant years in the 100 year timespan
	sort(death.begin(), death.end());

	int first_year = birth.front();// get first and last years to find out range
	int last_year = death.back();

	for (size_t i = first_year;i < last_year;i++)
	{
		year_count.insert({ i, 0 });//create relevant year range in map
	}
	
	for (size_t i = 0;i < persons.size();i++)
	{
		for (int j = persons[i].birth_year;j < persons[i].death_year;j++)
			
		{
			year_count.insert({ j, year_count.at(j)++ }); //increment value based on people alive that year
		}
		
	}
	max = (*max_element(year_count.begin(),year_count.end(), compare)); // find the year with max value and 
	for (auto& i : year_count) //then find other years if present with same value and add them to output vector
	{
		if (i.second == max.second)
		{
			years.push_back(i.first);
		}
	}


}

void main()
{


	vector<Person> persons;
	vector<int>years;

	persons.push_back(Person("Rohan",1920, 1925));
	persons.push_back(Person("Sunny",1923, 1927));
	persons.push_back(Person("Heena",1921, 1927));
	persons.push_back(Person("Pratik",1925, 1926));
	persons.push_back(Person("Ameya",1923, 1929));
	
	for (auto& i : persons)
	{
		cout<<"Name: " << i.name.c_str() << " B: " << i.birth_year << " D: " << i.death_year << endl;
	}
	
	YearMostPeopleAlive(persons, years);

	cout << "Years when maximum people were alive: " << endl;
	for (auto& i : years)
	{
		cout << i<<endl;
	}
	
	int a = 0;
}