package com.burhanstore.primewalls

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.widget.Button
import android.widget.ImageView
import android.widget.RatingBar
import android.widget.TextView
import com.google.android.gms.ads.nativead.MediaView
import com.google.android.gms.ads.nativead.NativeAd
import com.google.android.gms.ads.nativead.NativeAdView
import io.flutter.plugins.googlemobileads.GoogleMobileAdsPlugin.NativeAdFactory

class ListTileNativeAdFactory(private val context: Context) : NativeAdFactory {
    override fun createNativeAd(
        nativeAd: NativeAd,
        customOptions: Map<String, Any>?
    ): NativeAdView {
        val nativeAdView = LayoutInflater.from(context)
            .inflate(R.layout.list_tile_native_ad, null) as NativeAdView

        // Attribution
        val attributionViewSmall = nativeAdView
            .findViewById<TextView>(R.id.tv_list_tile_native_ad_attribution_small)
        val attributionViewLarge = nativeAdView
            .findViewById<TextView>(R.id.tv_list_tile_native_ad_attribution_large)
        val iconView = nativeAdView.findViewById<ImageView>(R.id.iv_list_tile_native_ad_icon)
        val icon = nativeAd.icon
        if (icon != null) {
            attributionViewSmall.visibility = View.VISIBLE
            attributionViewLarge.visibility = View.INVISIBLE
            iconView.setImageDrawable(icon.drawable)
        } else {
            attributionViewSmall.visibility = View.INVISIBLE
            attributionViewLarge.visibility = View.VISIBLE
        }
        nativeAdView.iconView = iconView

        // Headline
        val headlineView = nativeAdView.findViewById<TextView>(R.id.tv_list_tile_native_ad_headline)
        headlineView.text = nativeAd.headline
        nativeAdView.headlineView = headlineView

        // Body
        val bodyView = nativeAdView.findViewById<TextView>(R.id.tv_list_tile_native_ad_body)
        with(bodyView) {
            text = nativeAd.body
            visibility = if (nativeAd.body.isNullOrEmpty()) View.INVISIBLE else View.VISIBLE
        }
        nativeAdView.bodyView = bodyView

        // Media
        val mediaView = nativeAdView.findViewById<MediaView>(R.id.mv_list_tile_native_ad_media)
        nativeAdView.mediaView = mediaView

        // Call to Action
        val ctaView = nativeAdView.findViewById<Button>(R.id.btn_list_tile_native_ad_call_to_action)
        with(ctaView) {
            text = nativeAd.callToAction
            visibility = if (nativeAd.callToAction.isNullOrEmpty()) View.INVISIBLE else View.VISIBLE
        }
        nativeAdView.callToActionView = ctaView

        nativeAdView.setNativeAd(nativeAd)
        return nativeAdView
    }
}
