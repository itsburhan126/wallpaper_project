package com.burhanstore.primewalls

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.widget.Button
import android.widget.ImageView
import android.widget.TextView
import com.google.android.gms.ads.nativead.MediaView
import com.google.android.gms.ads.nativead.NativeAd
import com.google.android.gms.ads.nativead.NativeAdView
import io.flutter.plugins.googlemobileads.GoogleMobileAdsPlugin.NativeAdFactory

class ShortsNativeAdFactory(private val context: Context) : NativeAdFactory {
    override fun createNativeAd(
        nativeAd: NativeAd,
        customOptions: Map<String, Any>?
    ): NativeAdView {
        val nativeAdView = LayoutInflater.from(context)
            .inflate(R.layout.shorts_native_ad, null) as NativeAdView

        // Attribution
        val attributionView = nativeAdView
            .findViewById<TextView>(R.id.tv_shorts_native_ad_attribution)
        // Note: We don't need to set text for attribution, it's static "Ad"
        
        // Icon
        val iconView = nativeAdView.findViewById<ImageView>(R.id.iv_shorts_native_ad_icon)
        val icon = nativeAd.icon
        if (icon != null) {
            iconView.visibility = View.VISIBLE
            iconView.setImageDrawable(icon.drawable)
        } else {
            iconView.visibility = View.INVISIBLE
        }
        nativeAdView.iconView = iconView

        // Headline
        val headlineView = nativeAdView.findViewById<TextView>(R.id.tv_shorts_native_ad_headline)
        headlineView.text = nativeAd.headline
        nativeAdView.headlineView = headlineView

        // Body
        val bodyView = nativeAdView.findViewById<TextView>(R.id.tv_shorts_native_ad_body)
        with(bodyView) {
            text = nativeAd.body
            visibility = if (nativeAd.body.isNullOrEmpty()) View.INVISIBLE else View.VISIBLE
        }
        nativeAdView.bodyView = bodyView

        // Media
        val mediaView = nativeAdView.findViewById<MediaView>(R.id.mv_shorts_native_ad_media)
        nativeAdView.mediaView = mediaView

        // Call to Action
        val ctaView = nativeAdView.findViewById<Button>(R.id.btn_shorts_native_ad_call_to_action)
        with(ctaView) {
            text = nativeAd.callToAction
            visibility = if (nativeAd.callToAction.isNullOrEmpty()) View.INVISIBLE else View.VISIBLE
        }
        nativeAdView.callToActionView = ctaView

        nativeAdView.setNativeAd(nativeAd)
        return nativeAdView
    }
}
