from opensky_api import OpenSkyApi
import time

api = OpenSkyApi()
states = api.get_states()


print(states)