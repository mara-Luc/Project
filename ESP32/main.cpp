#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Keypad.h>
#include <ArduinoJson.h>

const char* ssid     = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";
String controllerHost = "http://raspberrypi.local:7000";

#define RST_PIN  22
#define SS_PIN   5
MFRC522 mfrc522(SS_PIN, RST_PIN);

const byte ROWS = 4, COLS = 4;
char keys[ROWS][COLS] = {
  {'1','2','3','A'},
  {'4','5','6','B'},
  {'7','8','9','C'},
  {'*','0','#','D'}
};
byte rowPins[ROWS] = {13, 12, 14, 27};
byte colPins[COLS] = {26, 25, 33, 32};
Keypad keypad = Keypad(makeKeymap(keys), rowPins, colPins, ROWS, COLS);

#define DOOR_PIN  4
const unsigned long UNLOCK_TIME_MS = 3000;

String pinBuffer = "";
const int PIN_LENGTH = 4;

void connectWiFi() {
  WiFi.begin(ssid, password);
  int retries = 0;
  while (WiFi.status() != WL_CONNECTED && retries < 50) {
    delay(500);
    retries++;
  }
}

String uidToString(MFRC522::Uid *uid) {
  String out = "";
  for (byte i = 0; i < uid->size; i++) {
    if (uid->uidByte[i] < 0x10) out += "0";
    out += String(uid->uidByte[i], HEX);
    if (i != uid->size - 1) out += " ";
  }
  out.toUpperCase();
  return out;
}

bool httpPostJson(const String& url, const String& jsonPayload, String& response) {
  if (WiFi.status() != WL_CONNECTED) connectWiFi();
  if (WiFi.status() != WL_CONNECTED) return false;

  HTTPClient http;
  http.begin(url);
  http.addHeader("Content-Type", "application/json");
  int httpCode = http.POST(jsonPayload);
  if (httpCode > 0) {
    response = http.getString();
    http.end();
    return true;
  }
  http.end();
  return false;
}

void unlockDoor() {
  digitalWrite(DOOR_PIN, HIGH);
  delay(UNLOCK_TIME_MS);
  digitalWrite(DOOR_PIN, LOW);
}

void checkRFID() {
  if (!mfrc522.PICC_IsNewCardPresent()) return;
  if (!mfrc522.PICC_ReadCardSerial()) return;

  String uidStr = uidToString(&mfrc522.uid);

  StaticJsonDocument<256> doc;
  doc["uid"] = uidStr;
  doc["camera_id"] = 3;
  String payload;
  serializeJson(doc, payload);

  String response;
  if (httpPostJson(controllerHost + "/controller/rfid", payload, response)) {
    StaticJsonDocument<256> respDoc;
    if (!deserializeJson(respDoc, response)) {
      bool success = respDoc["success"];
      const char* action = respDoc["action"];
      if (success && String(action) == "unlock") unlockDoor();
    }
  }

  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();
}

void checkKeypad() {
  char key = keypad.getKey();
  if (!key) return;

  if (key >= '0' && key <= '9') {
    pinBuffer += key;
    if (pinBuffer.length() >= PIN_LENGTH) {
      StaticJsonDocument<256> doc;
      doc["pin_hash"] = pinBuffer; // hash in production
      doc["camera_id"] = 3;
      String payload;
      serializeJson(doc, payload);

      String response;
      if (httpPostJson(controllerHost + "/controller/pin", payload, response)) {
        StaticJsonDocument<256> respDoc;
        if (!deserializeJson(respDoc, response)) {
          bool success = respDoc["success"];
          const char* action = respDoc["action"];
          if (success && String(action) == "unlock") unlockDoor();
        }
      }
      pinBuffer = "";
    }
  } else if (key == '*') {
    pinBuffer = "";
  }
}

void setup() {
  Serial.begin(115200);
  pinMode(DOOR_PIN, OUTPUT);
  digitalWrite(DOOR_PIN, LOW);
  connectWiFi();
  SPI.begin();
  mfrc522.PCD_Init();
}

void loop() {
  checkRFID();
  checkKeypad();
  delay(10);
}