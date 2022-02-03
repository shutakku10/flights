import requests
from opensky_api import OpenSkyApi
import time
from geopy.geocoders import Nominatim

plane = input("Von welchem Flugzeug willst du die aktuellen Daten?: ")

api = OpenSkyApi()
states = api.get_states(int(time.time()), plane)

#print(states)

if len(states.states) != 0:
    geolocator = Nominatim(user_agent="geoapiExercises")

    location = geolocator.reverse(str(states.states[0].latitude) +","+ str(states.states[0].longitude))

   # print(city + " - " + country)

    print("Sende das Zeug nun an den Server")

    URL = "http://fs201.de/flights.php"

    PARAMS = {
        'location': location,
        'origin': states.states[0].origin_country,
        'velocity': states.states[0].velocity
    }

    print(location)
    print(states.states[0].origin_country)
    print(states.states[0].velocity)

    r = requests.post(url = URL, params = PARAMS)
    print(r.content)
else:
    print("Der Flug wurde nicht gefunden :/")
