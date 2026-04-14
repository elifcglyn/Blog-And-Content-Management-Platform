import React, { useState, useEffect } from "react";
import { Link } from "react-router"; // react-router-dom kullanıyoruz

import { Avatar, AvatarFallback, AvatarImage } from "../components/ui/avatar";
import { Button } from "../components/ui/button";
import { Badge } from "../components/ui/badge";
import { 
  Mail, Settings, Calendar, History, ArrowUpRight, 
  X, Camera, Check, Github, Globe, Twitter, UserPlus, Heart 
} from "lucide-react";

export function ProfilePage() {
  const [activeTab, setActiveTab] = useState("posts");
  const [isLoading, setIsLoading] = useState(true);
  
  // 🚀 GERÇEK VERİ STATE'LERİ
  const [userPosts, setUserPosts] = useState<any[]>([]);
  const [userData, setUserData] = useState({
    id: 1, // Şimdilik 1 numaralı kullanıcı (Sen)
    name: "Emirhan",
    username: "Emirhan1351",
    bio: "Yükleniyor...",
    avatar: "https://ui-avatars.com/api/?name=Emirhan",
    email: "",
    kayit_tarihi: ""
  });

  const [isEditModalOpen, setIsEditModalOpen] = useState(false);
  const [isContactModalOpen, setIsContactModalOpen] = useState(false);
  const [isFollowing, setIsFollowing] = useState(false);
  const [followerCount, setFollowerCount] = useState(1240); // İleride bunu da DB'den çekeceğiz

  // 🚀 SAYFA YÜKLENDİĞİNDE VERİLERİ PHP'DEN ÇEK
  const kullaniciBilgileriniGetir = () => {
    fetch("http://localhost/Blog-And-Content-Management-Platform/api/kullanici_getir.php?id=1")
      .then(res => res.json())
      .then(data => {
        if(data && !data.error) {
          setUserData({
            id: data.id,
            name: data.ad_soyad || "İsimsiz Kullanıcı",
            username: data.username || "user",
            bio: data.bio || "Henüz bir biyografi yazılmadı.",
            avatar: data.avatar_url || `https://ui-avatars.com/api/?name=${data.ad_soyad}`,
            email: data.email || "",
            kayit_tarihi: data.kayit_tarihi || new Date().toISOString()
          });
        }
      })
      .catch(err => console.error("Kullanıcı çekme hatası:", err));
  };

  useEffect(() => {
    // 1. Kullanıcı Bilgilerini Çek
    kullaniciBilgileriniGetir();

    // 2. Bu Kullanıcının Yazılarını Çek
    fetch("http://localhost/Blog-And-Content-Management-Platform/api/yazilari_getir.php")
      .then(res => res.json())
      .then(data => {
        // Şimdilik tüm yazılardan sadece bu kullanıcıya (author_id = 1) ait olanları filtreliyoruz
        const myPosts = data.filter((post: any) => post.yazar_id === 1); 
        setUserPosts(myPosts);
        setIsLoading(false);
      })
      .catch(err => {
        console.error("Yazı çekme hatası:", err);
        setIsLoading(false);
      });
  }, []);

  // 🚀 GERÇEK VERİTABANI GÜNCELLEME FONKSİYONU
  const handleSave = () => {
    // PHP'ye yeni bilgileri (JSON olarak) fırlatıyoruz
    fetch("http://localhost/Blog-And-Content-Management-Platform/api/kullanici_guncelle.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id: userData.id,
        ad_soyad: userData.name,
        bio: userData.bio
      })
    })
    .then(res => res.json())
    .then(data => {
      if(data.success) {
        setIsEditModalOpen(false); // Başarılıysa pencereyi kapat
        kullaniciBilgileriniGetir(); // Veritabanındaki yeni haliyle ekranı tazeleyelim
      } else {
        alert("Bir hata oluştu: " + data.error);
      }
    })
    .catch(err => {
      console.error("Güncelleme hatası:", err);
      alert("Bağlantı hatası yaşandı!");
    });
  };

  if (isLoading) {
    return <div className="flex-1 flex items-center justify-center min-h-screen text-slate-500 font-serif italic text-2xl">Profil yükleniyor...</div>;
  }

  return (
    <div className="flex-1 flex flex-col bg-white dark:bg-slate-950 font-sans overflow-auto relative animate-in fade-in duration-500">
      <main className="flex-1 container mx-auto px-6 py-12 max-w-5xl">
        
        {/* PROFIL ÜST ALAN */}
        <section className="flex flex-col md:flex-row gap-10 items-center md:items-start mb-16 animate-in slide-in-from-bottom-6 duration-700">
          <div className="relative group">
            <div className="absolute -inset-2 bg-teal-500/20 rounded-full blur-xl opacity-50 group-hover:opacity-100 transition-opacity"></div>
            <Avatar className="h-40 w-40 ring-4 ring-white dark:ring-slate-900 shadow-2xl relative">
              <AvatarImage src={userData.avatar} />
              <AvatarFallback className="text-4xl font-serif italic">{userData.name.charAt(0)}</AvatarFallback>
            </Avatar>
          </div>

          <div className="flex-1 text-center md:text-left space-y-4">
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
              <div>
                <h1 className="font-serif text-5xl italic text-slate-900 dark:text-white tracking-tight leading-tight">
                  {userData.name}
                </h1>
                <p className="text-teal-600 font-medium tracking-widest uppercase text-xs mt-1">
                  @{userData.username} • Sistem Yöneticisi
                </p>
              </div>
              
              <div className="flex gap-2.5 justify-center shrink-0">
                <Button 
                  onClick={() => setIsEditModalOpen(true)}
                  variant="outline" 
                  className="rounded-full border-slate-200 hover:bg-slate-50 hover:border-teal-100 hover:text-teal-700 shadow-sm transition-all text-xs uppercase tracking-widest font-bold"
                >
                  <Settings className="w-4 h-4 mr-2" /> Profili Düzenle
                </Button>
              </div>
            </div>

            <p className="text-slate-500 dark:text-slate-400 text-lg leading-relaxed max-w-2xl italic font-light">
              "{userData.bio}"
            </p>

            <div className="flex flex-wrap justify-center md:justify-start gap-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-sm text-slate-400 font-medium">
              <div className="flex items-center gap-2">
                <Calendar className="w-4 h-4 text-teal-500" />
                <span>Üyelik: {new Date(userData.kayit_tarihi).toLocaleDateString('tr-TR')}</span>
              </div>
              <div className="flex items-center gap-2">
                <History className="w-4 h-4 text-teal-500" />
                <span>{userPosts.length} Yayınlanmış Yazı</span>
              </div>
            </div>
          </div>
        </section>

        {/* TAB NAVIGATION */}
        <div className="flex gap-8 border-b border-slate-100 dark:border-slate-800 mb-10">
          <button 
            onClick={() => setActiveTab("posts")}
            className={`pb-4 text-sm font-bold tracking-widest uppercase transition-all relative ${activeTab === "posts" ? "text-slate-900 dark:text-white" : "text-slate-400 hover:text-slate-600"}`}
          >
            Yazılarım
            {activeTab === "posts" && <div className="absolute bottom-0 left-0 w-full h-0.5 bg-teal-500 animate-in zoom-in" />}
          </button>
        </div>

        {/* 🚀 GERÇEK YAZI LİSTESİ */}
        <div className="grid gap-6 animate-in fade-in duration-700 delay-300">
          {userPosts.length > 0 ? (
            userPosts.map((post: any) => (
              <Link 
                key={post.id} 
                to={`/post/${post.id}`} // Slug yerine ID kullanıyoruz
                className="group bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-6 rounded-[2.5rem] hover:shadow-2xl hover:shadow-teal-500/5 transition-all flex flex-col md:flex-row items-center gap-6 relative"
              >
                <div className="w-full md:w-52 h-36 rounded-3xl overflow-hidden shrink-0 shadow-inner border border-slate-100">
                  <img src={post.kapak_resmi || "https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&q=80&w=2000"} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                </div>
                <div className="flex-1 space-y-3">
                  <div className="flex items-center gap-3">
                      <Badge variant="secondary" className="bg-slate-100 text-slate-600 text-[10px] uppercase font-bold tracking-wider">Genel</Badge>
                      <span className="text-[10px] text-slate-400 flex items-center gap-1 uppercase tracking-tighter">
                          <History className="w-3 h-3" /> {new Date(post.yayin_tarihi).toLocaleDateString('tr-TR')}
                      </span>
                  </div>
                  <h3 className="font-serif text-3xl italic text-slate-900 dark:text-white group-hover:text-teal-600 transition-colors leading-tight">
                    {post.baslik}
                  </h3>
                  {/* HTML etiketlerini temizleyip sadece düz metni gösteriyoruz (Özet olarak) */}
                  <p className="text-slate-500 line-clamp-2 font-light text-sm" dangerouslySetInnerHTML={{ __html: post.icerik.substring(0, 150) + "..." }}></p>
                </div>
                <div className="shrink-0 p-4 w-full md:w-auto flex justify-center">
                  <div className="w-14 h-14 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition-all shadow-inner">
                    <ArrowUpRight className="w-6 h-6" />
                  </div>
                </div>
              </Link>
            ))
          ) : (
            <div className="text-center py-20 bg-slate-50 dark:bg-slate-900 rounded-[3rem] border border-slate-100 dark:border-slate-800">
              <h3 className="text-2xl font-serif italic text-slate-500 mb-2">Henüz bir yazı paylaşmadın.</h3>
              <p className="text-slate-400 text-sm mb-6">İlk hikayeni anlatmak için harika bir gün!</p>
              <Link to="/new-post" className="bg-teal-600 text-white font-bold text-xs uppercase tracking-widest px-8 py-4 rounded-full hover:bg-teal-700 transition-colors shadow-lg shadow-teal-500/20 inline-flex items-center gap-2">
                Yeni Yazı Oluştur
              </Link>
            </div>
          )}
        </div>
      </main>

      {/* 🛠️ MODALLAR BURADA */}
      {isEditModalOpen && (
        <div className="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-md p-4 animate-in fade-in duration-200">
          <div className="bg-white dark:bg-slate-900 w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
            <div className="p-8 border-b border-slate-50 dark:border-slate-800 flex justify-between items-center">
              <h2 className="font-serif text-3xl italic tracking-tighter text-slate-900 dark:text-white">Profili Düzenle</h2>
              <button onClick={() => setIsEditModalOpen(false)} className="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors"><X className="w-5 h-5" /></button>
            </div>
            
            <div className="p-8 space-y-6">
              <div className="flex flex-col items-center gap-4">
                <div className="relative group cursor-pointer">
                  <Avatar className="h-28 w-28 ring-4 ring-teal-50 dark:ring-teal-900/30">
                    <AvatarImage src={userData.avatar} />
                  </Avatar>
                  <div className="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <Camera className="w-6 h-6 text-white" />
                  </div>
                </div>
                <p className="text-[10px] font-bold uppercase tracking-widest text-slate-400">Görseli Değiştir</p>
              </div>

              <div className="space-y-5">
                <div className="space-y-1.5">
                  <label className="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-2">Görünen Ad</label>
                  <input 
                    type="text" 
                    value={userData.name}
                    onChange={(e) => setUserData({...userData, name: e.target.value})}
                    className="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3.5 focus:ring-2 ring-teal-500 outline-none transition-all dark:text-white"
                  />
                </div>
                <div className="space-y-1.5">
                  <label className="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-2">Biyografi</label>
                  <textarea 
                    value={userData.bio}
                    onChange={(e) => setUserData({...userData, bio: e.target.value})}
                    rows={4}
                    className="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3.5 focus:ring-2 ring-teal-500 outline-none transition-all resize-none dark:text-white"
                  />
                </div>
              </div>
            </div>

            <div className="p-8 bg-slate-50 dark:bg-slate-800/50 flex gap-3">
              <Button onClick={() => setIsEditModalOpen(false)} variant="ghost" className="flex-1 rounded-full hover:bg-slate-200">İptal</Button>
              <Button onClick={handleSave} className="flex-1 rounded-full bg-teal-600 hover:bg-teal-700 text-white gap-2 shadow-lg shadow-teal-500/20">
                <Check className="w-4 h-4" /> Kaydet
              </Button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}