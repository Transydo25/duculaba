package com.sms.sendmessage;


import java.util.Date;

public class Data {
    String message;
    String number;
    String uuid;
    Date date;

    public Data(String message, String number, String uuid) {
        this.message = message;
        this.number = number;
        this.uuid = uuid;
        this.date = new Date();
    }

    public Date getDate() {
        return date;
    }

    public void setDate(Date date) {
        this.date = date;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public String getNumber() {
        return number;
    }

    public void setNumber(String number) {
        this.number = number;
    }

    public String getUuid() {
        return uuid;
    }

    public void setUuid(String uuid) {
        this.uuid = uuid;
    }
}
