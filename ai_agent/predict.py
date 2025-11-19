import json
import sys
from statistics import mean
from datetime import datetime


def load_payload():
    raw = sys.stdin.read()
    if not raw.strip():
        raise ValueError("Payload vacío")
    return json.loads(raw)


def seasonal_boost(weekly_sales, events):
    today = datetime.today()
    boost = 0
    for event in events:
        if event.get("month") == today.month:
            boost += 0.15
    return boost


def forecast_product(product, events):
    weekly = product.get("weekly_sales", [])
    if not weekly:
        return {
            "product_id": product["product_id"],
            "name": product["name"],
            "forecast" : 0,
            "trend": "sin_datos"
        }

    tail = weekly[-4:]
    base = mean(tail)
    trend = "estable"
    if len(tail) >= 2:
        if tail[-1] > tail[-2] * 1.15:
            trend = "alza"
        elif tail[-1] < tail[-2] * 0.85:
            trend = "baja"

    forecast = base * (1 + seasonal_boost(weekly, events))
    return {
        "product_id": product["product_id"],
        "name": product["name"],
        "forecast": round(forecast, 2),
        "trend": trend
    }


def build_response(payload):
    events = payload.get("seasonal_events", [])
    result = {
        "generated_at": payload.get("generated_at"),
        "forecast": [],
        "restock": [],
        "alerts": {
            "low_stock": [],
            "overstock": [],
            "expiring": [],
        }
    }

    for product in payload.get("products", []):
        forecast = forecast_product(product, events)
        result["forecast"].append(forecast)

        inventory = product.get("inventory", {})
        quantity = inventory.get("quantity") or 0
        expires_in = inventory.get("expires_in_days")

        needed = max(0, forecast["forecast"] * 2 - quantity)
        if needed > 0:
            result["restock"].append({
                "product_id": product["product_id"],
                "name": product["name"],
                "suggested_qty": round(needed),
                "reason": f"Demanda esperada {forecast['forecast']} uds vs stock {quantity} uds"
            })

        if quantity < forecast["forecast"]:
            result["alerts"]["low_stock"].append({
                "name": product["name"],
                "stock": quantity,
                "forecast": forecast["forecast"],
            })

        if quantity > forecast["forecast"] * 4:
            result["alerts"]["overstock"].append({
                "name": product["name"],
                "stock": quantity,
                "forecast": forecast["forecast"],
            })

        if expires_in is not None and expires_in <= 30:
            result["alerts"]["expiring"].append({
                "name": product["name"],
                "expires_in_days": expires_in,
                "stock": quantity,
            })

    return result


def main():
    payload = load_payload()
    response = build_response(payload)
    sys.stdout.write(json.dumps(response))


if __name__ == "__main__":
    main()
